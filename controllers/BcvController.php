<?php
/**
 * BcvController.php
 * Controlador para obtener la tasa oficial BCV (Banco Central de Venezuela)
 * desde la API pública ve.dolarapi.com.
 * Si el servidor no tiene acceso a red (entorno local XAMPP), devuelve
 * un valor de fallback y marca la respuesta como "offline".
 */

require_once __DIR__ . '/../config/database.php';

class BcvController
{
    /** URL de la API pública de tasas */
    private const API_URL = 'https://ve.dolarapi.com/v1/divisas/oficial';

    /** Tasa de fallback cuando no hay conectividad */
    private const TASA_FALLBACK = 42.50;

    /** Tiempo de vida del caché en segundos (1 hora) */
    private const CACHE_TTL = 3600;

    /** @var PDO|null Conexión a la base de datos */
    private ?PDO $db;

    public function __construct()
    {
        try {
            $database = new Database();
            $this->db = $database->getConnection();
        } catch (Exception $e) {
            // Si la BD no está disponible, continuamos sin caché
            $this->db = null;
        }
    }

    // -------------------------------------------------------------------------
    // Punto de entrada público (ruta /api/bcv)
    // -------------------------------------------------------------------------

    /**
     * Responde con JSON la tasa BCV actual.
     * Intenta: caché en BD → API externa → fallback hardcodeado.
     */
    public function api(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        try {
            $resultado = $this->getTasa();
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Error interno al obtener la tasa BCV.',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Lógica principal
    // -------------------------------------------------------------------------

    /**
     * Obtiene la tasa BCV usando la siguiente cascada:
     *  1. Caché en la tabla tipo_cambio_bcv (si existe y no ha expirado)
     *  2. API externa ve.dolarapi.com
     *  3. Valor de fallback (offline)
     *
     * @return array{tasa: float, fecha: string, fuente: string, cached: bool}
     */
    public function getTasa(): array
    {
        // 1. Intentar desde caché en BD
        $desde_cache = $this->leerCache();
        if ($desde_cache !== null) {
            return $desde_cache;
        }

        // 2. Intentar desde API externa
        $desde_api = $this->consultarApi();
        if ($desde_api !== null) {
            $this->guardarCache($desde_api['tasa'], $desde_api['fecha'], 'BCV');
            return array_merge($desde_api, ['cached' => false]);
        }

        // 3. Fallback offline
        $hoy = date('Y-m-d');
        $this->guardarCache(self::TASA_FALLBACK, $hoy, 'Fallback');
        return [
            'tasa'   => self::TASA_FALLBACK,
            'fecha'  => $hoy,
            'fuente' => 'Fallback',
            'cached' => false,
        ];
    }

    // -------------------------------------------------------------------------
    // Caché en base de datos
    // -------------------------------------------------------------------------

    /**
     * Lee el registro más reciente de la tabla tipo_cambio_bcv
     * siempre que no haya expirado el TTL.
     *
     * @return array|null  Datos de tasa o null si no hay caché válido.
     */
    private function leerCache(): ?array
    {
        if ($this->db === null) {
            return null;
        }

        try {
            $sql = "SELECT tasa, fecha, fuente, created_at
                    FROM tipo_cambio_bcv
                    ORDER BY id DESC
                    LIMIT 1";
            $stmt = $this->db->query($sql);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fila) {
                return null;
            }

            // Verificar si el caché sigue vigente
            $edad = time() - strtotime($fila['created_at']);
            if ($edad > self::CACHE_TTL) {
                return null; // expirado
            }

            return [
                'tasa'   => (float) $fila['tasa'],
                'fecha'  => $fila['fecha'],
                'fuente' => $fila['fuente'],
                'cached' => true,
            ];
        } catch (PDOException $e) {
            // La tabla no existe todavía; simplemente ignoramos el caché
            return null;
        }
    }

    /**
     * Guarda la tasa obtenida en la tabla tipo_cambio_bcv.
     * Crea la tabla si no existe antes de insertar.
     */
    private function guardarCache(float $tasa, string $fecha, string $fuente): void
    {
        if ($this->db === null) {
            return;
        }

        try {
            // Crear la tabla si no existe
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS tipo_cambio_bcv (
                    id         INT AUTO_INCREMENT PRIMARY KEY,
                    tasa       DECIMAL(18, 4) NOT NULL,
                    fecha      DATE NOT NULL,
                    fuente     VARCHAR(20) NOT NULL DEFAULT 'BCV',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $stmt = $this->db->prepare(
                "INSERT INTO tipo_cambio_bcv (tasa, fecha, fuente) VALUES (:tasa, :fecha, :fuente)"
            );
            $stmt->execute([
                ':tasa'   => $tasa,
                ':fecha'  => $fecha,
                ':fuente' => $fuente,
            ]);
        } catch (PDOException $e) {
            // Silencio: el guardado de caché es opcional; no interrumpimos la respuesta
        }
    }

    // -------------------------------------------------------------------------
    // Consulta a la API externa
    // -------------------------------------------------------------------------

    /**
     * Hace una solicitud HTTP a la API de dolarapi.com.
     * Primero intenta con cURL; si no está disponible, usa file_get_contents.
     * Retorna null ante cualquier error de red o parsing.
     *
     * @return array{tasa: float, fecha: string, fuente: string}|null
     */
    private function consultarApi(): ?array
    {
        $json = $this->fetchConCurl(self::API_URL);

        if ($json === null) {
            $json = $this->fetchConFileGetContents(self::API_URL);
        }

        if ($json === null) {
            return null;
        }

        $datos = json_decode($json, true);
        if (!is_array($datos) || !isset($datos['promedio'])) {
            return null;
        }

        return [
            'tasa'   => (float) $datos['promedio'],
            'fecha'  => $datos['fechaActualizacion'] ?? date('Y-m-d'),
            'fuente' => 'BCV',
        ];
    }

    /**
     * Descarga una URL con cURL con timeout corto para no bloquear la app.
     */
    private function fetchConCurl(string $url): ?string
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => 'CeroProject/1.0',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $respuesta = curl_exec($ch);
        $error     = curl_errno($ch);
        curl_close($ch);

        if ($error || $respuesta === false || $respuesta === '') {
            return null;
        }

        return $respuesta;
    }

    /**
     * Descarga una URL con file_get_contents como alternativa a cURL.
     */
    private function fetchConFileGetContents(string $url): ?string
    {
        $contexto = stream_context_create([
            'http' => [
                'method'          => 'GET',
                'header'          => "Accept: application/json\r\n",
                'timeout'         => 5,
                'ignore_errors'   => false,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        try {
            $respuesta = @file_get_contents($url, false, $contexto);
            return ($respuesta === false) ? null : $respuesta;
        } catch (Exception $e) {
            return null;
        }
    }
}

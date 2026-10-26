<?php
/**
 * ContabilidadController.php
 * Controlador del módulo contable de Cero ERP.
 *
 * Rutas gestionadas (ver public/index.php):
 *   GET  /contabilidad               → index()
 *   POST /contabilidad/asiento/crear → crearAsiento()
 *   POST /contabilidad/periodo/abrir → abrirPeriodo()
 *   GET  /contabilidad/libro-diario  → libroDiario()
 *   GET  /api/plan-cuentas           → planCuentas()
 *
 * Seguridad: todos los métodos verifican sesión activa y rol autorizado.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MotorContable.php';

class ContabilidadController
{
    /** Roles que pueden acceder al módulo contable */
    private const ROLES_AUTORIZADOS = ['Administrador', 'Contador'];

    /** @var PDO|null */
    private ?PDO $db;

    /** @var MotorContable|null */
    private ?MotorContable $motor;

    /** Prefijo de las URLs del sistema */
    private string $base_path = '/cero_project/public';

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function __construct()
    {
        try {
            $database    = new Database();
            $this->db    = $database->getConnection();
            $this->motor = ($this->db !== null) ? new MotorContable($this->db) : null;
        } catch (\Exception $e) {
            $this->db    = null;
            $this->motor = null;
        }
    }

    // =========================================================================
    // MÉTODOS PÚBLICOS (rutas)
    // =========================================================================

    /**
     * GET /contabilidad
     * Panel principal del módulo contable.
     * Muestra períodos, resumen del plan de cuentas y últimos asientos.
     */
    public function index(): void
    {
        $this->verificarAcceso();

        $periodos        = [];
        $cuentas_resumen = [];
        $ultimos_asientos = [];
        $error           = null;

        try {
            if ($this->motor !== null) {
                // Últimos períodos
                $periodos = $this->obtenerPeriodos();

                // Resumen del plan de cuentas (solo primer nivel)
                $plan_completo   = $this->motor->obtenerPlanCuentas();
                $cuentas_resumen = $this->resumirPlanCuentas($plan_completo);

                // Últimos 10 asientos confirmados
                $ultimos_asientos = $this->obtenerUltimosAsientos(10);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        // Exponer variables para la vista
        $usuario    = $_SESSION['nombre_usuario'] ?? 'Usuario';
        $rol        = $_SESSION['rol']            ?? '';
        $titulo     = 'Módulo Contable';

        // Incluir vista (si existe; de lo contrario, mostrar JSON de datos)
        $vista = __DIR__ . '/../views/contabilidad/index.php';
        if (file_exists($vista)) {
            include $vista;
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'modulo'          => 'Contabilidad',
                'periodos'        => $periodos,
                'cuentas_resumen' => $cuentas_resumen,
                'ultimos_asientos' => $ultimos_asientos,
                'error'           => $error,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    /**
     * POST /contabilidad/asiento/crear
     * Procesa el formulario de creación de un nuevo asiento contable.
     *
     * Espera los siguientes campos POST:
     *   - descripcion (string)
     *   - tipo        (string, opcional — default 'Manual')
     *   - cuentas[]   (array de IDs de cuentas)
     *   - tipos[]     (array 'Debe'|'Haber' para cada renglón)
     *   - montos[]    (array de importes numéricos)
     *   - descripciones[] (array de descripciones por renglón, opcional)
     */
    public function crearAsiento(): void
    {
        $this->verificarAcceso();
        $this->verificarMetodo('POST');

        header('Content-Type: application/json; charset=utf-8');

        if ($this->motor === null) {
            http_response_code(503);
            echo json_encode(['exito' => false, 'mensaje' => 'Base de datos no disponible.']);
            return;
        }

        try {
            // --- Recoger y validar campos básicos ---
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipo        = trim($_POST['tipo']        ?? 'Manual');

            if ($descripcion === '') {
                throw new \InvalidArgumentException("La descripción del asiento es obligatoria.");
            }

            // --- Construir array de renglones ---
            $cuentas       = $_POST['cuentas']       ?? [];
            $tipos         = $_POST['tipos']          ?? [];
            $montos        = $_POST['montos']         ?? [];
            $descripciones = $_POST['descripciones']  ?? [];

            if (count($cuentas) < 2) {
                throw new \InvalidArgumentException(
                    "Un asiento contable requiere al menos dos renglones (Debe y Haber)."
                );
            }

            $renglones = [];
            foreach ($cuentas as $i => $id_cuenta) {
                $renglones[] = [
                    'id_cuenta'   => (int) $id_cuenta,
                    'tipo'        => $tipos[$i]          ?? 'Debe',
                    'monto'       => (float) ($montos[$i] ?? 0),
                    'descripcion' => $descripciones[$i]  ?? '',
                ];
            }

            $id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);
            $id_asiento = $this->motor->crearAsiento($descripcion, $renglones, $id_usuario, $tipo);

            echo json_encode([
                'exito'      => true,
                'mensaje'    => "Asiento creado correctamente.",
                'id_asiento' => $id_asiento,
            ]);

        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /**
     * POST /contabilidad/periodo/abrir
     * Abre un nuevo período contable.
     *
     * Espera POST: anio (int), mes (int 1–12)
     */
    public function abrirPeriodo(): void
    {
        $this->verificarAcceso();
        $this->verificarMetodo('POST');

        header('Content-Type: application/json; charset=utf-8');

        if ($this->motor === null) {
            http_response_code(503);
            echo json_encode(['exito' => false, 'mensaje' => 'Base de datos no disponible.']);
            return;
        }

        try {
            $anio = (int) ($_POST['anio'] ?? 0);
            $mes  = (int) ($_POST['mes']  ?? 0);

            if ($anio < 2000 || $anio > 2100) {
                throw new \InvalidArgumentException("Año inválido: {$anio}.");
            }
            if ($mes < 1 || $mes > 12) {
                throw new \InvalidArgumentException("Mes inválido: {$mes}.");
            }

            $id_periodo = $this->motor->abrirPeriodo($anio, $mes);

            echo json_encode([
                'exito'      => true,
                'mensaje'    => "Período {$anio}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . " abierto correctamente.",
                'id_periodo' => $id_periodo,
            ]);

        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /contabilidad/libro-diario
     * Retorna el libro diario del período indicado.
     *
     * Query params: periodo_id (int)
     */
    public function libroDiario(): void
    {
        $this->verificarAcceso();

        header('Content-Type: application/json; charset=utf-8');

        if ($this->motor === null) {
            http_response_code(503);
            echo json_encode(['exito' => false, 'mensaje' => 'Base de datos no disponible.']);
            return;
        }

        try {
            $periodo_id = (int) ($_GET['periodo_id'] ?? 0);
            if ($periodo_id <= 0) {
                throw new \InvalidArgumentException("Debe indicar un periodo_id válido.");
            }

            $asientos = $this->motor->obtenerLibroDiario($periodo_id);

            echo json_encode([
                'exito'    => true,
                'periodo'  => $periodo_id,
                'asientos' => $asientos,
                'total'    => count($asientos),
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['exito' => false, 'mensaje' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /api/plan-cuentas
     * Retorna el árbol completo del plan de cuentas en JSON.
     */
    public function planCuentas(): void
    {
        $this->verificarAcceso();

        header('Content-Type: application/json; charset=utf-8');

        if ($this->motor === null) {
            http_response_code(503);
            echo json_encode(['exito' => false, 'mensaje' => 'Base de datos no disponible.']);
            return;
        }

        try {
            $arbol = $this->motor->obtenerPlanCuentas();
            echo json_encode([
                'exito'  => true,
                'cuentas' => $arbol,
                'total'   => $this->contarCuentas($arbol),
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['exito' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE APOYO
    // =========================================================================

    /**
     * Verifica que el usuario tenga sesión activa y rol autorizado.
     * Si no, redirige al login o devuelve 403 JSON según el contexto.
     */
    private function verificarAcceso(): void
    {
        if (!isset($_SESSION['id_usuario'])) {
            $this->redirigirOJson(
                $this->base_path . '/login',
                403,
                'No autenticado. Inicie sesión.'
            );
        }

        $rol = $_SESSION['rol'] ?? '';
        if (!in_array($rol, self::ROLES_AUTORIZADOS, true)) {
            $this->redirigirOJson(
                $this->base_path . '/dashboard',
                403,
                "Acceso denegado. Se requiere rol: " . implode(' o ', self::ROLES_AUTORIZADOS) . "."
            );
        }
    }

    /**
     * Verifica que la solicitud use el método HTTP esperado.
     *
     * @param string $metodo  'POST' | 'GET'
     */
    private function verificarMetodo(string $metodo): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodo)) {
            http_response_code(405);
            header('Allow: ' . strtoupper($metodo));
            echo json_encode(['exito' => false, 'mensaje' => "Método no permitido. Use {$metodo}."]);
            exit();
        }
    }

    /**
     * Redirige para peticiones normales; devuelve JSON para solicitudes AJAX.
     */
    private function redirigirOJson(string $url, int $codigo, string $mensaje): never
    {
        $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($esAjax) {
            http_response_code($codigo);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['exito' => false, 'mensaje' => $mensaje]);
        } else {
            header('Location: ' . $url);
        }
        exit();
    }

    /**
     * Obtiene los últimos N períodos contables de la BD.
     */
    private function obtenerPeriodos(int $limite = 12): array
    {
        if ($this->db === null) {
            return [];
        }
        try {
            $stmt = $this->db->prepare("
                SELECT id, anio, mes, estado, fecha_apertura, fecha_cierre
                FROM periodos_contables
                ORDER BY anio DESC, mes DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return []; // La tabla aún no existe
        }
    }

    /**
     * Obtiene los últimos N asientos confirmados.
     */
    private function obtenerUltimosAsientos(int $limite = 10): array
    {
        if ($this->db === null) {
            return [];
        }
        try {
            $stmt = $this->db->prepare("
                SELECT ac.id, ac.numero, ac.descripcion, ac.fecha, ac.tipo,
                       u.nombre AS usuario
                FROM asientos_contables ac
                LEFT JOIN usuarios u ON u.id = ac.id_usuario
                WHERE ac.estado = 'Confirmado'
                ORDER BY ac.id DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return []; // La tabla aún no existe
        }
    }

    /**
     * Extrae el primer nivel del árbol de cuentas para el resumen del panel.
     */
    private function resumirPlanCuentas(array $arbol): array
    {
        return array_map(function (array $cuenta) {
            return [
                'codigo'   => $cuenta['codigo'],
                'nombre'   => $cuenta['nombre'],
                'tipo'     => $cuenta['tipo'],
                'n_hijos'  => count($cuenta['hijos'] ?? []),
            ];
        }, $arbol);
    }

    /**
     * Cuenta recursivamente el total de cuentas en el árbol.
     */
    private function contarCuentas(array $nodos): int
    {
        $total = 0;
        foreach ($nodos as $nodo) {
            $total++;
            if (!empty($nodo['hijos'])) {
                $total += $this->contarCuentas($nodo['hijos']);
            }
        }
        return $total;
    }
}

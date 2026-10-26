<?php
/**
 * MotorContable.php
 * Modelo central del módulo contable de Cero ERP.
 *
 * Responsabilidades:
 *  - Gestión de períodos contables (apertura / cierre).
 *  - Creación de asientos con validación de partida doble.
 *  - Consulta de libro diario y plan de cuentas.
 *
 * Convenciones de numeración:
 *  - Asientos: ASI-YYYYMM-NNNN  (ej. ASI-202610-0001)
 *  - Períodos:  un solo registro por año/mes en la tabla periodos_contables.
 */
class MotorContable
{
    /** @var PDO Conexión activa a la base de datos */
    private PDO $db;

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    /**
     * @param PDO $db  Instancia PDO ya conectada (inyectada desde el controlador).
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        // Aseguramos excepciones en todas las operaciones
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // =========================================================================
    // PERÍODOS CONTABLES
    // =========================================================================

    /**
     * Verifica que exista un período contable en estado 'Abierto' que contenga
     * la fecha dada.
     *
     * @param  string $fecha  Fecha en formato YYYY-MM-DD.
     * @return array|null     Fila del período o null si no existe/está cerrado.
     * @throws \RuntimeException Si la tabla no existe.
     */
    public function verificarPeriodoAbierto(string $fecha): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, anio, mes, estado, fecha_apertura, fecha_cierre
                FROM periodos_contables
                WHERE estado = 'Abierto'
                  AND :fecha BETWEEN fecha_apertura AND fecha_cierre
                LIMIT 1
            ");
            $stmt->execute([':fecha' => $fecha]);
            $periodo = $stmt->fetch(PDO::FETCH_ASSOC);
            return $periodo ?: null;
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                "Error al verificar período contable: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Abre (crea) un nuevo período contable para el año y mes indicados.
     * Si ya existe un período para ese mes, lanza una excepción.
     *
     * @param  int $anio  Año del período (ej. 2026).
     * @param  int $mes   Mes del período (1–12).
     * @return int        ID del período recién creado.
     * @throws \RuntimeException
     */
    public function abrirPeriodo(int $anio, int $mes): int
    {
        $this->crearTablasContablesIfNotExist();

        try {
            // Verificar duplicado
            $stmtCheck = $this->db->prepare(
                "SELECT id FROM periodos_contables WHERE anio = :anio AND mes = :mes LIMIT 1"
            );
            $stmtCheck->execute([':anio' => $anio, ':mes' => $mes]);
            if ($stmtCheck->fetch()) {
                throw new \RuntimeException(
                    "Ya existe un período contable para {$anio}-{$mes}."
                );
            }

            // Calcular fechas del período (primer y último día del mes)
            $fechaApertura = sprintf('%04d-%02d-01', $anio, $mes);
            $fechaCierre   = date('Y-m-t', strtotime($fechaApertura));

            $stmt = $this->db->prepare("
                INSERT INTO periodos_contables (anio, mes, estado, fecha_apertura, fecha_cierre)
                VALUES (:anio, :mes, 'Abierto', :fecha_apertura, :fecha_cierre)
            ");
            $stmt->execute([
                ':anio'          => $anio,
                ':mes'           => $mes,
                ':fecha_apertura' => $fechaApertura,
                ':fecha_cierre'  => $fechaCierre,
            ]);

            return (int) $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                "Error al abrir período contable: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Cierra un período contable, impidiendo nuevos asientos en él.
     *
     * @param  int $id_periodo  ID del período a cerrar.
     * @return bool             true si se actualizó al menos un registro.
     * @throws \RuntimeException
     */
    public function cerrarPeriodo(int $id_periodo): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE periodos_contables
                SET estado = 'Cerrado', fecha_cierre_real = NOW()
                WHERE id = :id AND estado = 'Abierto'
            ");
            $stmt->execute([':id' => $id_periodo]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                "Error al cerrar período contable: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    // =========================================================================
    // ASIENTOS CONTABLES
    // =========================================================================

    /**
     * Crea un asiento contable completo con sus renglones.
     *
     * Proceso:
     *  1. Verifica período abierto para la fecha del asiento.
     *  2. Genera número correlativo (ASI-YYYYMM-NNNN).
     *  3. Inserta cabecera en asientos_contables.
     *  4. Inserta renglones en renglones_asiento (primero Debe, luego Haber).
     *  5. Valida partida doble contra vw_totales_asiento.
     *  6. Commit si cuadra; rollback y Exception si no cuadra.
     *
     * @param  string $descripcion  Descripción del asiento.
     * @param  array  $renglones    Array de renglones:
     *                              [['id_cuenta' => int, 'tipo' => 'Debe'|'Haber', 'monto' => float, 'descripcion' => string], ...]
     * @param  int    $id_usuario   ID del usuario que registra.
     * @param  string $tipo         Tipo de asiento (default 'Manual').
     * @return int                  ID del asiento creado.
     * @throws \RuntimeException    Si el período está cerrado o no cuadra la partida doble.
     */
    public function crearAsiento(
        string $descripcion,
        array  $renglones,
        int    $id_usuario,
        string $tipo = 'Manual'
    ): int {
        if (empty($renglones)) {
            throw new \InvalidArgumentException("El asiento debe tener al menos un renglón.");
        }

        $fecha = date('Y-m-d');

        // 1. Verificar período abierto
        $periodo = $this->verificarPeriodoAbierto($fecha);
        if ($periodo === null) {
            throw new \RuntimeException(
                "No existe un período contable abierto para la fecha {$fecha}."
            );
        }

        $this->db->beginTransaction();

        try {
            // 2. Número correlativo
            $numero = $this->generarNumeroAsiento($periodo['anio'], $periodo['mes']);

            // 3. Insertar cabecera
            $stmtCab = $this->db->prepare("
                INSERT INTO asientos_contables
                    (numero, descripcion, fecha, tipo, id_periodo, id_usuario, estado)
                VALUES
                    (:numero, :descripcion, :fecha, :tipo, :id_periodo, :id_usuario, 'Borrador')
            ");
            $stmtCab->execute([
                ':numero'      => $numero,
                ':descripcion' => $descripcion,
                ':fecha'       => $fecha,
                ':tipo'        => $tipo,
                ':id_periodo'  => $periodo['id'],
                ':id_usuario'  => $id_usuario,
            ]);
            $id_asiento = (int) $this->db->lastInsertId();

            // 4. Insertar renglones — primero Debe, luego Haber
            $debes   = array_filter($renglones, fn($r) => strtolower($r['tipo'] ?? '') === 'debe');
            $haberes = array_filter($renglones, fn($r) => strtolower($r['tipo'] ?? '') === 'haber');

            $orden = 1;
            foreach (array_merge(array_values($debes), array_values($haberes)) as $renglon) {
                $this->insertarRenglon($id_asiento, $renglon, $orden++);
            }

            // 5. Validar partida doble
            $this->validarPartidaDoble($id_asiento);

            // 6. Marcar asiento como Confirmado y hacer commit
            $this->db->prepare(
                "UPDATE asientos_contables SET estado = 'Confirmado' WHERE id = :id"
            )->execute([':id' => $id_asiento]);

            $this->db->commit();
            return $id_asiento;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e; // re-lanzar para que el controlador la maneje
        }
    }

    // =========================================================================
    // CONSULTAS (LIBRO DIARIO / PLAN DE CUENTAS)
    // =========================================================================

    /**
     * Retorna los asientos del libro diario para un período dado,
     * con sus renglones y datos de cuenta.
     *
     * @param  int $periodo_id  ID del período contable.
     * @return array            Array de asientos con renglones anidados.
     * @throws \RuntimeException
     */
    public function obtenerLibroDiario(int $periodo_id): array
    {
        try {
            // Cabeceras
            $stmtCab = $this->db->prepare("
                SELECT
                    ac.id,
                    ac.numero,
                    ac.descripcion,
                    ac.fecha,
                    ac.tipo,
                    ac.estado,
                    u.nombre AS usuario
                FROM asientos_contables ac
                LEFT JOIN usuarios u ON u.id = ac.id_usuario
                WHERE ac.id_periodo = :periodo_id
                  AND ac.estado = 'Confirmado'
                ORDER BY ac.fecha ASC, ac.numero ASC
            ");
            $stmtCab->execute([':periodo_id' => $periodo_id]);
            $asientos = $stmtCab->fetchAll(PDO::FETCH_ASSOC);

            if (empty($asientos)) {
                return [];
            }

            // Renglones de cada asiento
            $stmtReng = $this->db->prepare("
                SELECT
                    ra.id_asiento,
                    ra.orden,
                    ra.tipo,
                    ra.monto,
                    ra.descripcion,
                    pc.codigo     AS cuenta_codigo,
                    pc.nombre     AS cuenta_nombre
                FROM renglones_asiento ra
                JOIN plan_cuentas pc ON pc.id = ra.id_cuenta
                WHERE ra.id_asiento = :id_asiento
                ORDER BY ra.orden ASC
            ");

            foreach ($asientos as &$asiento) {
                $stmtReng->execute([':id_asiento' => $asiento['id']]);
                $asiento['renglones'] = $stmtReng->fetchAll(PDO::FETCH_ASSOC);
            }
            unset($asiento);

            return $asientos;
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                "Error al obtener libro diario: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Retorna el plan de cuentas completo en estructura de árbol.
     * Las cuentas sin padre (id_padre = NULL) son raíces del árbol.
     *
     * @return array  Árbol de cuentas anidado.
     * @throws \RuntimeException
     */
    public function obtenerPlanCuentas(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT id, codigo, nombre, tipo, nivel, id_padre, permite_movimiento
                FROM plan_cuentas
                ORDER BY codigo ASC
            ");
            $flat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->construirArbol($flat);
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                "Error al obtener plan de cuentas: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    // =========================================================================
    // MÉTODOS PRIVADOS DE APOYO
    // =========================================================================

    /**
     * Genera el próximo número correlativo de asiento para el período dado.
     * Formato: ASI-YYYYMM-NNNN
     */
    private function generarNumeroAsiento(int $anio, int $mes): string
    {
        $prefijo = sprintf('ASI-%04d%02d-', $anio, $mes);

        $stmt = $this->db->prepare("
            SELECT numero FROM asientos_contables
            WHERE numero LIKE :prefijo
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([':prefijo' => $prefijo . '%']);
        $ultimo = $stmt->fetchColumn();

        $secuencia = 1;
        if ($ultimo) {
            // Extraer la parte numérica final (últimos 4 caracteres)
            $partes    = explode('-', $ultimo);
            $secuencia = ((int) end($partes)) + 1;
        }

        return $prefijo . str_pad((string) $secuencia, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Inserta un renglón individual en la tabla renglones_asiento.
     *
     * @param int   $id_asiento ID del asiento padre.
     * @param array $renglon    Datos del renglón.
     * @param int   $orden      Posición del renglón dentro del asiento.
     */
    private function insertarRenglon(int $id_asiento, array $renglon, int $orden): void
    {
        // Validación mínima del renglón
        if (!isset($renglon['id_cuenta'], $renglon['tipo'], $renglon['monto'])) {
            throw new \InvalidArgumentException(
                "Renglón inválido: se requieren 'id_cuenta', 'tipo' y 'monto'."
            );
        }
        if (!in_array(strtolower($renglon['tipo']), ['debe', 'haber'], true)) {
            throw new \InvalidArgumentException(
                "El tipo de renglón debe ser 'Debe' o 'Haber'."
            );
        }
        if ((float) $renglon['monto'] <= 0) {
            throw new \InvalidArgumentException(
                "El monto de cada renglón debe ser mayor que cero."
            );
        }

        $stmt = $this->db->prepare("
            INSERT INTO renglones_asiento
                (id_asiento, id_cuenta, tipo, monto, descripcion, orden)
            VALUES
                (:id_asiento, :id_cuenta, :tipo, :monto, :descripcion, :orden)
        ");
        $stmt->execute([
            ':id_asiento'  => $id_asiento,
            ':id_cuenta'   => (int) $renglon['id_cuenta'],
            ':tipo'        => ucfirst(strtolower($renglon['tipo'])),
            ':monto'       => (float) $renglon['monto'],
            ':descripcion' => $renglon['descripcion'] ?? '',
            ':orden'       => $orden,
        ]);
    }

    /**
     * Valida que el asiento esté cuadrado (total Debe == total Haber).
     * Usa la vista vw_totales_asiento si existe; en caso contrario calcula
     * directamente desde renglones_asiento.
     *
     * @param int $id_asiento
     * @throws \RuntimeException Si la partida doble no cuadra.
     */
    private function validarPartidaDoble(int $id_asiento): void
    {
        $totales = $this->obtenerTotalesAsiento($id_asiento);

        $debe  = round((float) ($totales['total_debe']  ?? 0), 4);
        $haber = round((float) ($totales['total_haber'] ?? 0), 4);

        if ($debe !== $haber) {
            throw new \RuntimeException(sprintf(
                "Partida doble no cuadra: Debe=%.2f, Haber=%.2f. Diferencia=%.2f.",
                $debe,
                $haber,
                abs($debe - $haber)
            ));
        }

        if ($debe == 0) {
            throw new \RuntimeException(
                "El asiento no puede tener montos en cero."
            );
        }
    }

    /**
     * Obtiene los totales Debe/Haber de un asiento.
     * Prioriza la vista vw_totales_asiento; si no existe, calcula directo.
     */
    private function obtenerTotalesAsiento(int $id_asiento): array
    {
        // Intentar con la vista
        try {
            $stmt = $this->db->prepare(
                "SELECT total_debe, total_haber FROM vw_totales_asiento WHERE id_asiento = :id"
            );
            $stmt->execute([':id' => $id_asiento]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fila) {
                return $fila;
            }
        } catch (\PDOException $e) {
            // La vista no existe; calculamos manualmente
        }

        // Cálculo directo desde renglones_asiento
        $stmt = $this->db->prepare("
            SELECT
                SUM(CASE WHEN tipo = 'Debe'  THEN monto ELSE 0 END) AS total_debe,
                SUM(CASE WHEN tipo = 'Haber' THEN monto ELSE 0 END) AS total_haber
            FROM renglones_asiento
            WHERE id_asiento = :id
        ");
        $stmt->execute([':id' => $id_asiento]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_debe' => 0, 'total_haber' => 0];
    }

    /**
     * Convierte una lista plana de cuentas en un árbol jerárquico.
     *
     * @param  array $flat  Lista plana con campos: id, id_padre, ...
     * @return array        Lista de nodos raíz con 'hijos' anidados.
     */
    private function construirArbol(array $flat): array
    {
        $mapa = [];
        foreach ($flat as &$cuenta) {
            $cuenta['hijos'] = [];
            $mapa[$cuenta['id']] = &$cuenta;
        }
        unset($cuenta);

        $raices = [];
        foreach ($mapa as &$cuenta) {
            if ($cuenta['id_padre'] === null || !isset($mapa[$cuenta['id_padre']])) {
                $raices[] = &$cuenta;
            } else {
                $mapa[$cuenta['id_padre']]['hijos'][] = &$cuenta;
            }
        }
        unset($cuenta);

        return $raices;
    }

    /**
     * Crea las tablas del módulo contable si no existen todavía.
     * Se llama antes de operaciones de escritura para garantizar el esquema.
     */
    private function crearTablasContablesIfNotExist(): void
    {
        // Tabla de períodos
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS periodos_contables (
                id                INT AUTO_INCREMENT PRIMARY KEY,
                anio              SMALLINT     NOT NULL,
                mes               TINYINT      NOT NULL,
                estado            ENUM('Abierto','Cerrado') NOT NULL DEFAULT 'Abierto',
                fecha_apertura    DATE         NOT NULL,
                fecha_cierre      DATE         NOT NULL,
                fecha_cierre_real DATETIME     NULL,
                created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_periodo (anio, mes)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Tabla de plan de cuentas
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS plan_cuentas (
                id                  INT AUTO_INCREMENT PRIMARY KEY,
                codigo              VARCHAR(20)  NOT NULL UNIQUE,
                nombre              VARCHAR(150) NOT NULL,
                tipo                ENUM('Activo','Pasivo','Patrimonio','Ingreso','Gasto','Costo') NOT NULL,
                nivel               TINYINT      NOT NULL DEFAULT 1,
                id_padre            INT          NULL,
                permite_movimiento  TINYINT(1)   NOT NULL DEFAULT 1,
                created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_padre) REFERENCES plan_cuentas(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Tabla de asientos contables (cabecera)
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS asientos_contables (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                numero      VARCHAR(20)  NOT NULL UNIQUE,
                descripcion VARCHAR(500) NOT NULL,
                fecha       DATE         NOT NULL,
                tipo        VARCHAR(50)  NOT NULL DEFAULT 'Manual',
                estado      ENUM('Borrador','Confirmado','Anulado') NOT NULL DEFAULT 'Borrador',
                id_periodo  INT          NOT NULL,
                id_usuario  INT          NOT NULL,
                created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_periodo) REFERENCES periodos_contables(id),
                FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Tabla de renglones del asiento (partidas individuales)
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS renglones_asiento (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                id_asiento  INT            NOT NULL,
                id_cuenta   INT            NOT NULL,
                tipo        ENUM('Debe','Haber') NOT NULL,
                monto       DECIMAL(18, 4) NOT NULL,
                descripcion VARCHAR(300)   NOT NULL DEFAULT '',
                orden       SMALLINT       NOT NULL DEFAULT 1,
                FOREIGN KEY (id_asiento) REFERENCES asientos_contables(id) ON DELETE CASCADE,
                FOREIGN KEY (id_cuenta)  REFERENCES plan_cuentas(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

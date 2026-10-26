<?php
require_once __DIR__ . '/../config/database.php';

class DashboardController {
    public function index() {
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: /cero_project/public/login');
            exit();
        }

        $db = (new Database())->getConnection();

        // SEMÁFORO DE RUTAS
        if ($_SESSION['rol'] == 'Paciente') {
            // Obtenemos los especialistas para que el paciente los vea y agende
            $stmt = $db->query("SELECT nombre_completo, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE r.nombre_rol IN ('Especialista', 'Entrenador')");
            $especialistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtenemos el ID real del paciente para sus citas
            $stmt_paciente = $db->prepare("SELECT id_paciente FROM pacientes WHERE correo = ?");
            $stmt_paciente->execute([$_SESSION['correo']]);
            $paciente = $stmt_paciente->fetch(PDO::FETCH_ASSOC);
            $id_paciente = $paciente ? $paciente['id_paciente'] : 0;

            require_once __DIR__ . '/../views/paciente/dashboard.php';
        } else {
            // ESTADÍSTICAS REALES PARA LA INTRANET DEL STAFF
            $dashboardError = null;
            $stats = ['pacientes' => 0, 'citas_hoy' => 0, 'crioterapia' => 0];
            $citas = [];
            $pacientes = [];
            $paciente_evolucion = null;
            $paciente_kpi = null;
            $paciente_recuperacion = null;
            $plan_nutricional_actual = null;
            $plan_nutricionales = [];
            $planes_entrenamiento = [];
            $kpi_historial = [];
            $recuperacion_historial = [];
            $empleados = [];
            $servicios = [];
            $coaches = [];

            require_once __DIR__ . '/../models/Paciente.php';
            require_once __DIR__ . '/../helpers/paciente_helper.php';
            require_once __DIR__ . '/../models/KPI.php';
            require_once __DIR__ . '/../models/Recuperacion.php';
            require_once __DIR__ . '/../models/PlanNutricional.php';
            require_once __DIR__ . '/../models/PlanEntrenamiento.php';
            $pacienteModel = new Paciente($db);
            $kpiModel = new KPI($db);
            $recuperacionModel = new Recuperacion($db);
            $planNutricionalModel = new PlanNutricional($db);
            $planEntrenamientoModel = new PlanEntrenamiento($db);

            try {
                $stats['pacientes'] = (int)$db->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
                $stats['citas_hoy'] = (int)$db->query("SELECT COUNT(*) FROM citas WHERE estado_cita = 'Pendiente'")->fetchColumn();
                $stats['crioterapia'] = (int)$db->query("SELECT COUNT(*) FROM recuperacion_termica WHERE DATE(fecha_sesion) = CURDATE()")->fetchColumn();

                $citas_filtro_raw = isset($_GET['citas_cedula']) ? trim((string) $_GET['citas_cedula']) : '';
                $citas_busqueda_sin_paciente = false;
                $citas_paciente_resumen = null;

                if ($citas_filtro_raw !== '') {
                    $citas_id_resuelto = $pacienteModel->resolverIdPorIdOCedula($citas_filtro_raw);
                    if ($citas_id_resuelto === null) {
                        $citas_busqueda_sin_paciente = true;
                        $citas = [];
                    } else {
                        $citas_paciente_resumen = $pacienteModel->obtenerPorId($citas_id_resuelto);
                        if ($citas_paciente_resumen) {
                            setCurrentPaciente($citas_id_resuelto);
                            $_SESSION['paciente_nombre'] = $citas_paciente_resumen['nombre_completo'] ?? '';
                        }
                        $stmt_citas = $db->prepare("SELECT c.*, p.nombre_completo, p.cedula, s.nombre_servicio FROM citas c
                            JOIN pacientes p ON c.id_paciente = p.id_paciente
                            LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
                            WHERE c.id_paciente = :id
                              AND c.estado_cita <> 'Archivada'
                            ORDER BY c.fecha_hora DESC LIMIT 200");
                        $stmt_citas->bindValue(':id', $citas_id_resuelto, PDO::PARAM_INT);
                        $stmt_citas->execute();
                        $citas = $stmt_citas->fetchAll(PDO::FETCH_ASSOC);
                    }
                } else {
                    $stmt_citas = $db->query("SELECT c.*, p.nombre_completo, p.cedula, s.nombre_servicio FROM citas c
                                          JOIN pacientes p ON c.id_paciente = p.id_paciente
                                          LEFT JOIN servicios s ON c.id_servicio = s.id_servicio
                                          WHERE c.estado_cita <> 'Rechazada'
                                            AND c.estado_cita <> 'Archivada'
                                          ORDER BY c.fecha_hora DESC LIMIT 20");
                    $citas = $stmt_citas ? $stmt_citas->fetchAll(PDO::FETCH_ASSOC) : [];
                }

                $pacientes = $pacienteModel->obtenerTodos() ?: [];

                if (isset($_GET['evolucion_id']) && trim((string) $_GET['evolucion_id']) !== '') {
                    $idPac = $pacienteModel->resolverIdPorIdOCedula($_GET['evolucion_id']);
                    if ($idPac !== null) {
                        $paciente_evolucion = $pacienteModel->obtenerPorId($idPac);
                        if ($paciente_evolucion) {
                            setCurrentPaciente($idPac);
                            $_SESSION['paciente_nombre'] = $paciente_evolucion['nombre_completo'] ?? '';
                            $_SESSION['paciente_cedula'] = $paciente_evolucion['cedula'] ?? '';
                            require_once __DIR__ . '/../models/Evolucion.php';
                            $evolucionModel = new Evolucion($db);
                            $evoluciones = $evolucionModel->obtenerPorPaciente($idPac) ?: [];
                            $plan_nutricional_actual = $planNutricionalModel->obtenerUltimoPorPaciente($idPac);
                            $plan_nutricionales = $planNutricionalModel->obtenerPorPaciente($idPac) ?: [];
                            $planes_entrenamiento = $planEntrenamientoModel->obtenerPorPaciente($idPac) ?: [];
                        }
                    }
                }

                if (isset($_GET['kpi_id']) && trim((string) $_GET['kpi_id']) !== '') {
                    $idPac = $pacienteModel->resolverIdPorIdOCedula($_GET['kpi_id']);
                    if ($idPac !== null) {
                        $paciente_kpi = $pacienteModel->obtenerPorId($idPac);
                        if ($paciente_kpi) {
                            setCurrentPaciente($idPac);
                            $_SESSION['paciente_nombre'] = $paciente_kpi['nombre_completo'] ?? '';
                            $_SESSION['paciente_cedula'] = $paciente_kpi['cedula'] ?? '';
                            $kpi_historial = $kpiModel->obtenerPorPaciente($idPac) ?: [];
                        }
                    }
                }

                if (isset($_GET['recuperacion_id']) && trim((string) $_GET['recuperacion_id']) !== '') {
                    $idPac = $pacienteModel->resolverIdPorIdOCedula($_GET['recuperacion_id']);
                    if ($idPac !== null) {
                        $paciente_recuperacion = $pacienteModel->obtenerPorId($idPac);
                        if ($paciente_recuperacion) {
                            setCurrentPaciente($idPac);
                            $_SESSION['paciente_nombre'] = $paciente_recuperacion['nombre_completo'] ?? '';
                            $_SESSION['paciente_cedula'] = $paciente_recuperacion['cedula'] ?? '';
                            $recuperacion_historial = $recuperacionModel->obtenerPorPaciente($idPac) ?: [];
                        }
                    }
                }

                if ($_SESSION['rol'] == 'Administrador') {
                    require_once __DIR__ . '/../models/Usuario.php';
                    $usuario = new Usuario($db);
                    $empleados = $usuario->listarEmpleados() ?: [];
                }

                if (in_array($_SESSION['rol'], ['Administrador', 'Recepcionista'])) {
                    require_once __DIR__ . '/../models/Servicio.php';
                    $servicioModel = new Servicio($db);
                    $servicios = $servicioModel->obtenerTodos() ?: [];
                }

                // REPORTES GERENCIALES (Solo para Administrador)
                $reporte_financiero = [];
                $reporte_patologias = [];
                $reporte_servicios = [];
                $total_citas_servicios = 0;

                if ($_SESSION['rol'] === 'Administrador') {
                    // Reporte 9: Consolidado Financiero (Corrigiendo Inyección SQL)
                    $fecha_inicio_rep9 = $_GET['fecha_inicio_rep9'] ?? null;
                    $fecha_fin_rep9 = $_GET['fecha_fin_rep9'] ?? null;
                    $params9 = [];
                    $sql9 = "SELECT s.nombre_servicio, 
                                   COUNT(c.id_cita) AS volumen, 
                                   (COUNT(c.id_cita) * s.precio) AS ingresos 
                            FROM citas c 
                            JOIN servicios s ON c.id_servicio = s.id_servicio 
                            WHERE c.estado_cita IN ('Confirmada', 'Completada')";
                    
                    if (!empty($fecha_inicio_rep9)) {
                        $sql9 .= " AND DATE(c.fecha_hora) >= :inicio";
                        $params9[':inicio'] = $fecha_inicio_rep9;
                    }
                    if (!empty($fecha_fin_rep9)) {
                        $sql9 .= " AND DATE(c.fecha_hora) <= :fin";
                        $params9[':fin'] = $fecha_fin_rep9;
                    }
                    $sql9 .= " GROUP BY s.id_servicio, s.nombre_servicio, s.precio ORDER BY ingresos DESC";
                    $stmt9 = $db->prepare($sql9);
                    $stmt9->execute($params9);
                    $reporte_financiero = $stmt9->fetchAll(PDO::FETCH_ASSOC);

                    // Reporte 10: Patologías
                    $reporte_patologias = $db->query("SELECT diagnostico, COUNT(*) AS casos FROM evolucion_medica WHERE diagnostico != '' GROUP BY diagnostico ORDER BY casos DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

                    // Reporte 11: Servicios Más Demandados (con filtro de fecha opcional)
                    $fecha_inicio_rep11 = $_GET['fecha_inicio_rep11'] ?? null;
                    $fecha_fin_rep11 = $_GET['fecha_fin_rep11'] ?? null;
                    $params11 = [];
                    $where11 = " WHERE c.estado_cita IN ('Confirmada', 'Completada')";
                    if (!empty($fecha_inicio_rep11)) {
                        $where11 .= " AND DATE(c.fecha_hora) >= :inicio11";
                        $params11[':inicio11'] = $fecha_inicio_rep11;
                    }
                    if (!empty($fecha_fin_rep11)) {
                        $where11 .= " AND DATE(c.fecha_hora) <= :fin11";
                        $params11[':fin11'] = $fecha_fin_rep11;
                    }

                    $stmt_tot = $db->prepare("SELECT COUNT(*) FROM citas c" . $where11);
                    $stmt_tot->execute($params11);
                    $total_citas_servicios = (int)$stmt_tot->fetchColumn();

                    $sql11 = "SELECT s.nombre_servicio, COUNT(c.id_cita) AS cantidad_citas FROM citas c JOIN servicios s ON c.id_servicio = s.id_servicio" . $where11 . " GROUP BY s.id_servicio, s.nombre_servicio ORDER BY cantidad_citas DESC";
                    $stmt11 = $db->prepare($sql11);
                    $stmt11->execute($params11);
                    $reporte_servicios = $stmt11->fetchAll(PDO::FETCH_ASSOC);

                    // Inteligencia de Negocios - métricas adicionales para administrador
                    $estado_citas_dist = $db->query("SELECT estado_cita, COUNT(*) AS total FROM citas GROUP BY estado_cita")->fetchAll(PDO::FETCH_ASSOC);
                    $pacientes_tendencia = $db->query("SELECT DATE_FORMAT(fecha_registro, '%Y-%m') AS periodo, COUNT(*) AS total FROM pacientes GROUP BY periodo ORDER BY periodo ASC")->fetchAll(PDO::FETCH_ASSOC);

                    $total_confirmadas = (int)$db->query("SELECT COUNT(*) FROM citas WHERE estado_cita = 'Confirmada'")->fetchColumn();
                    $total_completadas = (int)$db->query("SELECT COUNT(*) FROM citas WHERE estado_cita = 'Completada'")->fetchColumn();
                    $probabilidad_completada = $total_confirmadas > 0 ? round(($total_completadas / $total_confirmadas) * 100, 1) : 0;

                    $stmtRechazo = $db->query("SELECT s.nombre_servicio, SUM(c.estado_cita = 'Rechazada') AS rechazadas, COUNT(*) AS total_citas FROM citas c JOIN servicios s ON c.id_servicio = s.id_servicio GROUP BY s.id_servicio, s.nombre_servicio ORDER BY rechazadas DESC");
                    $probabilidad_rechazo_servicio = [];
                    foreach ($stmtRechazo->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $porcentaje = $row['total_citas'] > 0 ? round(($row['rechazadas'] / $row['total_citas']) * 100, 1) : 0;
                        $probabilidad_rechazo_servicio[] = [
                            'nombre_servicio' => $row['nombre_servicio'],
                            'rechazadas' => (int)$row['rechazadas'],
                            'total_citas' => (int)$row['total_citas'],
                            'porcentaje_rechazo' => $porcentaje,
                        ];
                    }

                    $stmtRetorno = $db->query("SELECT COUNT(*) AS total_pacientes, SUM(citas_multiples > 1) AS pacientes_retorno FROM (SELECT id_paciente, COUNT(*) AS citas_multiples FROM citas GROUP BY id_paciente) sub");
                    $retornoResults = $stmtRetorno->fetch(PDO::FETCH_ASSOC);
                    $probabilidad_retorno_paciente = 0;
                    if ($retornoResults && $retornoResults['total_pacientes'] > 0) {
                        $probabilidad_retorno_paciente = round(((int)$retornoResults['pacientes_retorno'] / (int)$retornoResults['total_pacientes']) * 100, 1);
                    }
                }

                $coaches = $db->query("SELECT u.id_usuario, u.nombre_completo FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE r.nombre_rol = 'Entrenador' ORDER BY u.nombre_completo ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            } catch (Throwable $exception) {
                $dashboardError = 'Error al cargar el panel de control. Intente nuevamente más tarde.';
                error_log('DashboardController error: ' . $exception->getMessage());
                $stats = ['pacientes' => 0, 'citas_hoy' => 0, 'crioterapia' => 0];
                $citas = [];
                $pacientes = [];
                $empleados = [];
                $servicios = [];
                $coaches = [];
                $plan_nutricionales = [];
                $planes_entrenamiento = [];
                $kpi_historial = [];
                $recuperacion_historial = [];
            }

            require_once __DIR__ . '/../views/intranet/dashboard.php';
        }
    }
}
?>

<?php
require_once '../config/database.php';

class CitaController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function agendar() {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Paciente') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        $fecha_hora = $_POST['fecha_hora'];

        $checkQuery = "SELECT COUNT(*) FROM citas WHERE fecha_hora = :fecha_hora AND estado_cita IN ('Pendiente', 'Confirmada')";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':fecha_hora', $fecha_hora);
        $checkStmt->execute();
        $existing = (int)$checkStmt->fetchColumn();

        if ($existing > 0) {
            echo "<script>alert('Ya existe una cita programada para esa misma hora. Por favor elige otro horario.'); window.location.href='/cero_project/public/dashboard';</script>";
            exit();
        }

        $query = "INSERT INTO citas (id_paciente, id_usuario, id_servicio, fecha_hora, estado_cita) 
                  VALUES (:paciente, :usuario, :servicio, :fecha, 'Pendiente')";
        $stmt = $this->db->prepare($query);

        $usuario_id = $_SESSION['id_usuario'];

        $stmt->bindParam(':paciente', $_POST['id_paciente']);
        $stmt->bindParam(':usuario', $usuario_id);
        $stmt->bindParam(':servicio', $_POST['id_servicio']);
        $stmt->bindParam(':fecha', $fecha_hora);

        if ($stmt->execute()) {
            echo "<script>alert('Cita enviada correctamente. Queda pendiente de confirmación.'); window.location.href='/cero_project/public/dashboard';</script>";
        }
    }

    public function registrar() {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] === 'Paciente') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once '../views/citas/registro.php';
            return;
        }

        $id_paciente = $_POST['id_paciente'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $fecha_hora = $fecha . ' ' . $hora . ':00';
        $motivo = $_POST['motivo'];
        $id_servicio = $_POST['id_servicio'];
        $id_usuario = $_SESSION['id_usuario'] ?? 1;
        $estado_cita = 'Pendiente';

        $query = "INSERT INTO citas (id_paciente, id_usuario, id_servicio, fecha_hora, estado_cita, mensaje_admin) VALUES (:id_paciente, :id_usuario, :id_servicio, :fecha_hora, :estado_cita, :mensaje_admin)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_paciente', $id_paciente);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_servicio', $id_servicio);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->bindParam(':estado_cita', $estado_cita);
        $stmt->bindParam(':mensaje_admin', $motivo);

        if ($stmt->execute()) {
            header('Location: /cero_project/public/cita/registrar?success=1');
            exit();
        } else {
            header('Location: /cero_project/public/cita/registrar?error=1');
            exit();
        }
    }

    public function cita_publica() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/');
            exit();
        }

        require_once '../models/Paciente.php';
        $pacienteModel = new Paciente($this->db);

        $nombre_completo = trim($_POST['nombre']) . ' ' . trim($_POST['apellido']);
        $cedula = trim($_POST['cedula']);

        if (!ctype_digit($cedula)) {
            echo "<script>alert('el campo cedula solamente registra numeros'); window.history.back();</script>";
            exit();
        }

        $telefono = trim($_POST['telefono']);
        $tipo_telefono = 'Móvil';
        $fecha_hora = $_POST['fecha_hora'];
        $area = strtolower(trim($_POST['area']));

        // Map area to servicio ID
        $servicio_map = [
            'fisioterapia' => 1,
            'nutricion' => 3,
            'acondicionamiento' => 2,
            'crioterapia' => 4
        ];
        $id_servicio = $servicio_map[$area] ?? 1; // Default fisioterapia

        // Crear/Obtener paciente
        $id_paciente = $pacienteModel->registrar_sin_usuario($cedula, $nombre_completo, $telefono, $tipo_telefono);

        if ($id_paciente) {
            try {
                if (!isset($servicio_map[$area]) || !is_numeric($id_servicio)) {
                    throw new Exception("Servicio inválido para área: {$area}");
                }
                $query = "INSERT INTO citas (id_paciente, id_usuario, id_servicio, fecha_hora, estado_cita) 
                          VALUES (:paciente, NULL, :servicio, :fecha, 'Pendiente')";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':paciente', $id_paciente);
                $stmt->bindParam(':servicio', $id_servicio);
                $stmt->bindParam(':fecha', $fecha_hora);

                if ($stmt->execute()) {
                    echo "<script>alert('¡Cita registrada exitosamente! Te contactaremos pronto.'); window.location.href='/cero_project/public/';</script>";
                } else {
                    $error = $stmt->errorInfo();
                    echo "<script>alert('Error al registrar la cita: " . addslashes($error[2]) . "'); window.location.href='/cero_project/public/';</script>";
                }
            } catch (PDOException $e) {
                echo "<script>alert('Error de BD al registrar cita (servicio inválido?): " . addslashes($e->getMessage()) . "'); window.location.href='/cero_project/public/';</script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location.href='/cero_project/public/';</script>";
            }
        } else {
            echo "<script>alert('Error al registrar el paciente. Verifica la cédula.'); window.location.href='/cero_project/public/';</script>";
        }
    }
    // Procesar cambio de estado (Confirmar / Completar)
    public function actualizar_estado() {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Administrador', 'Recepcionista'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cita = filter_var($_POST['id_cita'], FILTER_VALIDATE_INT);
            $estado = trim($_POST['estado']);
            
            if ($id_cita && in_array($estado, ['Confirmada', 'Completada', 'Rechazada'])) {
                $query = "UPDATE citas SET estado_cita = :estado WHERE id_cita = :id_cita";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':id_cita', $id_cita);
                
                if ($stmt->execute()) {
                    header('Location: /cero_project/public/dashboard?tab=citas&success=Estado actualizado');
                    exit();
                }
            }
            header('Location: /cero_project/public/dashboard?tab=citas&error=Error al procesar');
            exit();
        }
    }

    // Procesar eliminación o rechazo de citas
    public function eliminar() {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Administrador', 'Recepcionista'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cita = filter_var($_POST['id_cita'], FILTER_VALIDATE_INT);
            $accion = isset($_POST['accion']) ? trim($_POST['accion']) : 'rechazar';
            $nuevo_estado = $accion === 'archivar' ? 'Archivada' : 'Rechazada';

            if ($id_cita) {
                $query = "UPDATE citas SET estado_cita = :estado WHERE id_cita = :id_cita";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':estado', $nuevo_estado);
                $stmt->bindParam(':id_cita', $id_cita);
                
                if ($stmt->execute()) {
                    $mensaje = $nuevo_estado === 'Archivada' ? 'Cita archivada correctamente' : 'Cita rechazada correctamente';
                    header('Location: /cero_project/public/dashboard?tab=citas&success=' . urlencode($mensaje));
                    exit();
                }
            }
            $error_mensaje = $nuevo_estado === 'Archivada' ? 'Error al archivar la cita' : 'Error al rechazar la cita';
            header('Location: /cero_project/public/dashboard?tab=citas&error=' . urlencode($error_mensaje));
            exit();
        }
    }
}
?>
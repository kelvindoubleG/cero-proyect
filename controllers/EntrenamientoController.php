<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/PlanEntrenamiento.php';
require_once __DIR__ . '/../models/Paciente.php';

class EntrenamientoController {
    private $db;
    private $planEntrenamiento;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->planEntrenamiento = new PlanEntrenamiento($this->db);
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['Administrador', 'Entrenador'])) {
            echo "<script>alert('Acceso denegado: solo Administradores y Entrenadores pueden registrar planes de entrenamiento.'); window.location.href='/cero_project/public/dashboard';</script>";
            exit();
        }

        $id_paciente = isset($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
        if ($id_paciente <= 0 && isset($_SESSION['paciente_id'])) {
            $id_paciente = (int) $_SESSION['paciente_id'];
        }
        $id_usuario = isset($_POST['id_usuario']) ? (int) $_POST['id_usuario'] : 0;
        $objetivo_deportivo = trim($_POST['objetivo_deportivo'] ?? '');
        $descripcion_rutina = trim($_POST['descripcion_rutina'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin = trim($_POST['fecha_fin'] ?? '');

        if ($id_paciente <= 0 || $id_usuario <= 0 || $objetivo_deportivo === '' || $descripcion_rutina === '' || $fecha_inicio === '' || $fecha_fin === '') {
            echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
            exit();
        }

        $pacienteModel = new Paciente($this->db);
        if (!$pacienteModel->obtenerPorId($id_paciente)) {
            echo "<script>alert('Paciente no encontrado. Verifique el ID seleccionado.'); window.history.back();</script>";
            exit();
        }

        $stmt = $this->db->prepare("SELECT u.id_usuario FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.id_usuario = :id_usuario AND r.nombre_rol = 'Entrenador' LIMIT 1");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<script>alert('Coach no válido. Seleccione un entrenador registrado.'); window.history.back();</script>";
            exit();
        }

        if ($this->planEntrenamiento->registrar($id_paciente, $id_usuario, $objetivo_deportivo, $descripcion_rutina, $fecha_inicio, $fecha_fin)) {
            require_once __DIR__ . '/../helpers/paciente_helper.php';
            // Asegurar que la sesión mantiene el paciente seleccionado
            setCurrentPaciente($id_paciente);
            $ev = (int) $id_paciente;
            // Redirigir al tab de expediente (evolución) y mantener el paciente abierto
            echo "<script>alert('Plan de entrenamiento registrado correctamente.'); window.location.href='/cero_project/public/dashboard?tab=evolucion&evolucion_id={$ev}';</script>";
        } else {
            echo "<script>alert('Error al guardar el plan de entrenamiento. Intente nuevamente.'); window.history.back();</script>";
        }
        exit();
    }

    public function eliminar() {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Administrador', 'Entrenador'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        $id_plan = filter_var($_POST['id_plan'] ?? null, FILTER_VALIDATE_INT);
        $id_paciente = filter_var($_POST['id_paciente'] ?? null, FILTER_VALIDATE_INT);

        if (!$id_plan) {
            $url = '/cero_project/public/dashboard?tab=evolucion' . ($id_paciente ? '&evolucion_id=' . $id_paciente : '');
            header('Location: ' . $url . '&error=' . urlencode('Plan inválido.'));
            exit();
        }

        if ($this->planEntrenamiento->eliminar($id_plan)) {
            $url = '/cero_project/public/dashboard?tab=evolucion' . ($id_paciente ? '&evolucion_id=' . $id_paciente : '');
            header('Location: ' . $url . '&success=' . urlencode('Plan de entrenamiento eliminado.'));
            exit();
        }

        $url = '/cero_project/public/dashboard?tab=evolucion' . ($id_paciente ? '&evolucion_id=' . $id_paciente : '');
        header('Location: ' . $url . '&error=' . urlencode('No se pudo eliminar el plan.'));
        exit();
    }

    public function actualizar_estado() {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Administrador', 'Entrenador'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        $id_plan = filter_var($_POST['id_plan'] ?? null, FILTER_VALIDATE_INT);
        $estado = trim($_POST['estado'] ?? '');
        $id_paciente = filter_var($_POST['id_paciente'] ?? null, FILTER_VALIDATE_INT);
        $estados_validos = ['Activo', 'Inactivo', 'Completado'];

        if (!$id_plan || !in_array($estado, $estados_validos, true)) {
            $query = '/cero_project/public/dashboard?tab=evolucion' . ($id_paciente ? '&evolucion_id=' . $id_paciente : '');
            header('Location: ' . $query . '&error=' . urlencode('Datos de actualización inválidos.'));
            exit();
        }

        if ($this->planEntrenamiento->actualizarEstado($id_plan, $estado)) {
            $query = '/cero_project/public/dashboard?tab=evolucion' . ($id_paciente ? '&evolucion_id=' . $id_paciente : '');
            header('Location: ' . $query . '&success=' . urlencode('Estado del plan actualizado.'));
            exit();
        }

        $query = '/cero_project/public/dashboard?tab=evolucion' . ($id_paciente ? '&evolucion_id=' . $id_paciente : '');
        header('Location: ' . $query . '&error=' . urlencode('No se pudo actualizar el estado del plan.'));
        exit();
    }
}
?>
<?php
require_once '../config/database.php';
require_once '../models/Evolucion.php';

class EvolucionController {
    private $db;
    private $evolucion;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->evolucion = new Evolucion($this->db);
    }

    public function registrar() {
        // La sesión ya fue iniciada en el index.php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validación estricta de Roles: Solo Especialistas o Admin pueden registrar esto
            if ($_SESSION['rol'] != 'Especialista' && $_SESSION['rol'] != 'Administrador') {
                echo "<script>alert('Acceso Denegado: Rol no autorizado para registrar evoluciones médicas.'); window.location.href='/cero_project/public/dashboard';</script>";
                exit();
            }

            $id_paciente = isset($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
            if ($id_paciente <= 0 && isset($_SESSION['paciente_id'])) {
                $id_paciente = (int) $_SESSION['paciente_id'];
            }
            $id_usuario = $_SESSION['id_usuario']; // El ID del doctor logueado
            $diagnostico = $_POST['diagnostico'];
            $tratamiento = $_POST['tratamiento'];

            // Validar que el paciente existe
            require_once '../models/Paciente.php';
            $pacienteModel = new Paciente($this->db);
            $paciente = $pacienteModel->obtenerPorId($id_paciente);
            if (!$paciente) {
                echo "<script>alert('Paciente no encontrado. Verifique el ID.'); window.history.back();</script>";
                exit();
            }

            if ($this->evolucion->registrar($id_paciente, $id_usuario, $diagnostico, $tratamiento)) {
                require_once __DIR__ . '/../helpers/paciente_helper.php';
                // Mantener paciente en sesión
                setCurrentPaciente($id_paciente);
                $ev = (int) $id_paciente;
                echo "<script>alert('¡Evolución médica guardada con éxito en el expediente!'); window.location.href='/cero_project/public/dashboard?tab=evolucion&evolucion_id={$ev}';</script>";
            } else {
                echo "<script>alert('Error de base de datos al guardar.'); window.history.back();</script>";
            }
            exit();
        }
    }
}
?>
<?php
require_once '../config/database.php';
require_once '../models/Paciente.php';

class PacienteController {
    private $db;
    private $paciente;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->paciente = new Paciente($this->db);
    }

    public function index() {
        // Obtenemos los pacientes y cargamos la vista
        $lista_pacientes = $this->paciente->obtenerTodos();
        require_once '../views/intranet/pacientes.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cedula = trim($_POST['cedula']);
            $nombre = trim($_POST['nombre_completo']);
            $fecha = $_POST['fecha_nacimiento'];

            // Validación de cédula numérica por seguridad
            if (!ctype_digit($cedula)) {
                header('Location: /cero_project/public/pacientes?msg=error_numerico');
                exit();
            }

            if ($this->paciente->registrar($cedula, $nombre, $fecha)) {
                // Si guarda bien, redirecciona con éxito
                header('Location: /cero_project/public/pacientes?msg=exito');
            } else {
                // Si la cédula se repite, redirecciona con error
                header('Location: /cero_project/public/pacientes?msg=error');
            }
            exit();
        }
    }

    public function ver($id_paciente) {
        $paciente_data = $this->paciente->obtenerPorId($id_paciente);
        if ($paciente_data) {
            require_once '../views/intranet/ver_paciente.php';
        } else {
            header('Location: /cero_project/public/dashboard?error=Paciente no encontrado');
            exit();
        }
    }

    public function eliminar($id_paciente) {
        if ($this->paciente->eliminar($id_paciente)) {
            header('Location: /cero_project/public/dashboard?success=Paciente eliminado');
        } else {
            header('Location: /cero_project/public/dashboard?error=Error al eliminar paciente');
        }
        exit();
    }
}
?>
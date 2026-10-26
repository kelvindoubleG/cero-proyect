<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Recuperacion.php';
require_once __DIR__ . '/../models/Paciente.php';

class RecuperacionController {
    private $db;
    private $recuperacion;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->recuperacion = new Recuperacion($this->db);
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard?tab=recuperacion');
            exit();
        }

        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Paciente') {
            echo "<script>alert('Acceso denegado.'); window.location.href='/cero_project/public/dashboard';</script>";
            exit();
        }

        $id_paciente = isset($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
        if ($id_paciente <= 0 && isset($_SESSION['paciente_id'])) {
            $id_paciente = (int) $_SESSION['paciente_id'];
        }
        $tipo = isset($_POST['tipo_terapia']) ? trim((string) $_POST['tipo_terapia']) : '';
        $permitidos = ['Crioterapia', 'Sauna'];
        if (!in_array($tipo, $permitidos, true)) {
            echo "<script>alert('Tipo de terapia no válido.'); window.history.back();</script>";
            exit();
        }

        $duracion = isset($_POST['duracion_minutos']) ? (int) $_POST['duracion_minutos'] : 15;
        if ($duracion < 1 || $duracion > 480) {
            $duracion = 15;
        }

        if ($id_paciente <= 0) {
            echo "<script>alert('Indique un ID de paciente válido.'); window.history.back();</script>";
            exit();
        }

        $pacienteModel = new Paciente($this->db);
        if (!$pacienteModel->obtenerPorId($id_paciente)) {
            echo "<script>alert('Paciente no encontrado. Verifique el ID.'); window.history.back();</script>";
            exit();
        }

        if ($this->recuperacion->registrar($id_paciente, $tipo, $duracion)) {
            $rid = $id_paciente;
            echo "<script>alert('Sesión de recuperación registrada.'); window.location.href='/cero_project/public/dashboard?tab=recuperacion&recuperacion_id={$rid}';</script>";
        } else {
            echo "<script>alert('Error al guardar en base de datos.'); window.history.back();</script>";
        }
        exit();
    }
}

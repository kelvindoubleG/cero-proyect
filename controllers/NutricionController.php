<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/PlanNutricional.php';
require_once __DIR__ . '/../models/Paciente.php';

class NutricionController {
    private $db;
    private $planNutricional;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->planNutricional = new PlanNutricional($this->db);
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard?tab=evolucion');
            exit();
        }

        if ($_SESSION['rol'] != 'Especialista' && $_SESSION['rol'] != 'Administrador') {
            echo "<script>alert('Acceso denegado: solo personal autorizado puede registrar planes nutricionales.'); window.location.href='/cero_project/public/dashboard';</script>";
            exit();
        }

        $id_paciente = isset($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
        if ($id_paciente <= 0 && isset($_SESSION['paciente_id'])) {
            $id_paciente = (int) $_SESSION['paciente_id'];
        }
        $id_usuario = (int) $_SESSION['id_usuario'];
        $calorias = isset($_POST['calorias_totales']) ? (float) $_POST['calorias_totales'] : 0;
        $prot = isset($_POST['proteinas_g']) ? (int) $_POST['proteinas_g'] : 0;
        $cho = isset($_POST['carbohidratos_g']) ? (int) $_POST['carbohidratos_g'] : 0;
        $grasas = isset($_POST['grasas_g']) ? (int) $_POST['grasas_g'] : 0;

        if ($id_paciente <= 0 || $calorias <= 0 || $prot < 0 || $cho < 0 || $grasas < 0) {
            echo "<script>alert('Datos inválidos. Revise calorías y macronutrientes.'); window.history.back();</script>";
            exit();
        }

        $pacienteModel = new Paciente($this->db);
        if (!$pacienteModel->obtenerPorId($id_paciente)) {
            echo "<script>alert('Paciente no encontrado. Verifique el ID.'); window.history.back();</script>";
            exit();
        }

        $distribucion = "Prot: {$prot} g; CHO: {$cho} g; Grasas: {$grasas} g";

        if ($this->planNutricional->registrar($id_paciente, $id_usuario, $calorias, $distribucion, null)) {
            $ev = (int) $id_paciente;
            echo "<script>alert('Plan nutricional guardado en el expediente.'); window.location.href='/cero_project/public/dashboard?tab=evolucion&evolucion_id={$ev}';</script>";
        } else {
            echo "<script>alert('Error al guardar en base de datos.'); window.history.back();</script>";
        }
        exit();
    }
}

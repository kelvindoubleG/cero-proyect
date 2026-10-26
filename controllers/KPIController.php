<?php
require_once '../config/database.php';

class KPIController {
    public function registrar() {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $db = (new Database())->getConnection();
            $query = "INSERT INTO kpi_progresos (id_paciente, fecha_medicion, peso_kg, fuerza_maxima, rango_movimiento)\n                      VALUES (:id, CURDATE(), :peso, :fuerza, :rom)";
            $stmt = $db->prepare($query);
            $id_paciente = isset($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : 0;
            if ($id_paciente <= 0 && isset($_SESSION['paciente_id'])) {
                $id_paciente = (int) $_SESSION['paciente_id'];
            }
            $stmt->bindParam(':id', $id_paciente);
            $stmt->bindParam(':peso', $_POST['peso_kg']);
            $stmt->bindParam(':fuerza', $_POST['fuerza_maxima']);
            $rangoMov = isset($_POST['rango_movimiento']) ? $_POST['rango_movimiento'] : null;
            $stmt->bindParam(':rom', $rangoMov);

            if ($stmt->execute()) {
                $idPaciente = intval($_POST['id_paciente']);
                echo "<script>alert('Métricas actualizadas exitosamente.'); window.location.href='/cero_project/public/dashboard?tab=kpi&kpi_id={$idPaciente}';</script>";
            } else {
                echo "<script>alert('Error al guardar.'); window.history.back();</script>";
            }
        }
    }
}
?>

<?php
class KPI {
    private $conn;

    public function __construct($db) { $this->conn = $db; }

    public function registrar($id_paciente, $peso, $fuerza, $rom) {
        $query = "INSERT INTO kpi_progresos (id_paciente, fecha_medicion, peso_kg, fuerza_maxima, rango_movimiento) 
                  VALUES (:id, CURDATE(), :peso, :fuerza, :rom)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_paciente);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':fuerza', $fuerza);
        $stmt->bindParam(':rom', $rom);
        return $stmt->execute();
    }

    public function obtenerPorPaciente($id_paciente) {
        $query = "SELECT * FROM kpi_progresos WHERE id_paciente = ? ORDER BY fecha_medicion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_paciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
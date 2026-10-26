<?php
class Evolucion {
    private $conn;
    private $table_name = "evolucion_medica";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($id_paciente, $id_usuario, $diagnostico, $tratamiento) {
        $query = "INSERT INTO " . $this->table_name . " (id_paciente, id_usuario, fecha_evaluacion, diagnostico, tratamiento) 
                  VALUES (:id_paciente, :id_usuario, CURDATE(), :diagnostico, :tratamiento)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_paciente', $id_paciente);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':diagnostico', $diagnostico);
        $stmt->bindParam(':tratamiento', $tratamiento);
        
        return $stmt->execute();
    }

    public function obtenerPorPaciente($id_paciente) {
        $query = "SELECT e.*, u.nombre_completo as especialista 
                  FROM " . $this->table_name . " e 
                  JOIN usuarios u ON e.id_usuario = u.id_usuario 
                  WHERE e.id_paciente = :id_paciente 
                  ORDER BY e.fecha_evaluacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_paciente', $id_paciente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
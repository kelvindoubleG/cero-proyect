<?php
class Recuperacion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($id_paciente, $tipo_terapia, $duracion_minutos) {
        $query = "INSERT INTO recuperacion_termica (id_paciente, tipo_terapia, duracion_minutos) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id_paciente, $tipo_terapia, $duracion_minutos]);
    }

    public function obtenerPorPaciente($id_paciente) {
        $query = "SELECT * FROM recuperacion_termica WHERE id_paciente = ? ORDER BY fecha_sesion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_paciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

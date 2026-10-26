<?php
class PlanNutricional {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($id_paciente, $id_usuario, $requerimiento_calorico, $distribucion_macros) {
        $query = "INSERT INTO planes_nutricionales (id_paciente, id_usuario, requerimiento_calorico, distribucion_macros)
                  VALUES (:id_paciente, :id_usuario, :kcal, :macros)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_paciente', $id_paciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':kcal', $requerimiento_calorico);
        $stmt->bindParam(':macros', $distribucion_macros);
        return $stmt->execute();
    }

    public function obtenerUltimoPorPaciente($id_paciente) {
        $query = "SELECT p.cedula, p.nombre_completo, pn.requerimiento_calorico, pn.distribucion_macros
                  FROM planes_nutricionales pn
                  JOIN pacientes p ON pn.id_paciente = p.id_paciente
                  WHERE pn.id_paciente = ?
                  ORDER BY pn.fecha_registro DESC
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_paciente]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function obtenerPorPaciente($id_paciente) {
        $query = "SELECT pn.id_plan_nutricional, pn.requerimiento_calorico, pn.distribucion_macros, pn.fecha_registro
                  FROM planes_nutricionales pn
                  WHERE pn.id_paciente = ?
                  ORDER BY pn.fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_paciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

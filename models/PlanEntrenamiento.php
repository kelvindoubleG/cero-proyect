<?php
class PlanEntrenamiento {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($id_paciente, $id_usuario, $objetivo_deportivo, $descripcion_rutina, $fecha_inicio, $fecha_fin) {
        $query = "INSERT INTO planes_entrenamiento (id_paciente, id_usuario, objetivo_deportivo, descripcion_rutina, fecha_inicio, fecha_fin, estado_plan)"
               . " VALUES (:id_paciente, :id_usuario, :objetivo, :descripcion, :fecha_inicio, :fecha_fin, 'Activo')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_paciente', $id_paciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':objetivo', $objetivo_deportivo);
        $stmt->bindParam(':descripcion', $descripcion_rutina);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        return $stmt->execute();
    }

    public function actualizarEstado($id_plan, $estado_plan) {
        $query = "UPDATE planes_entrenamiento SET estado_plan = :estado_plan WHERE id_plan = :id_plan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado_plan', $estado_plan);
        $stmt->bindParam(':id_plan', $id_plan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id_plan) {
        $query = "DELETE FROM planes_entrenamiento WHERE id_plan = :id_plan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_plan', $id_plan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPorPaciente($id_paciente) {
        $query = "SELECT pe.id_plan, pe.objetivo_deportivo, pe.descripcion_rutina, pe.fecha_inicio, pe.fecha_fin, pe.estado_plan, u.nombre_completo AS coach
                  FROM planes_entrenamiento pe
                  JOIN usuarios u ON pe.id_usuario = u.id_usuario
                  WHERE pe.id_paciente = ?
                  ORDER BY pe.fecha_inicio DESC, pe.fecha_fin DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id_paciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
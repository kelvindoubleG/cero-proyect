<?php
class Servicio {
    private $conn;
    private $table_name = "servicios";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtiene todos los servicios con sus precios actuales
     */
    public function obtenerTodos() {
        $query = "SELECT id_servicio, nombre_servicio, tipo_servicio, precio 
                  FROM " . $this->table_name . " 
                  ORDER BY nombre_servicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un servicio específico por ID
     */
    public function obtenerPorId($id_servicio) {
        $query = "SELECT id_servicio, nombre_servicio, tipo_servicio, precio 
                  FROM " . $this->table_name . " 
                  WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_servicio, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el precio de un servicio
     */
    public function actualizarPrecio($id_servicio, $precio) {
        $query = "UPDATE " . $this->table_name . " 
                  SET precio = :precio 
                  WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_servicio, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Obtiene los precios actuales de todos los servicios (para reportes)
     */
    public function obtenerPreciosActuales() {
        $query = "SELECT id_servicio, nombre_servicio, precio 
                  FROM " . $this->table_name . " 
                  ORDER BY id_servicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el precio de un servicio específico (para cálculos rápidos)
     */
    public function obtenerPrecioPorId($id_servicio) {
        $query = "SELECT precio FROM " . $this->table_name . " 
                  WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_servicio, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['precio'] : 0.00;
    }
}
?>
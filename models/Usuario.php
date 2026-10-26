<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function contarUsuarios() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Registro para el personal (Admin, Especialistas, etc.)
    public function registrarAdmin($nombre, $correo, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " (id_rol, nombre_completo, correo, password_hash) 
                  VALUES (1, :nombre, :correo, :hash)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':hash', $hash);
        return $stmt->execute();
    }

    // Registro para el personal con rol variable
    public function registrarPersonal($nombre, $correo, $password, $id_rol) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table_name . " (id_rol, nombre_completo, correo, password_hash) 
                  VALUES (:id_rol, :nombre, :correo, :hash)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':hash', $hash);
        return $stmt->execute();
    }

    public function verificarLogin($correo, $password) {
        $query = "SELECT u.*, r.nombre_rol 
                  FROM usuarios u 
                  JOIN roles r ON u.id_rol = r.id_rol 
                  WHERE u.correo = :correo LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public function listarEmpleados() {
        $query = "SELECT u.id_usuario, u.nombre_completo, u.correo, r.nombre_rol, u.id_rol 
                  FROM usuarios u 
                  JOIN roles r ON u.id_rol = r.id_rol 
                  WHERE u.id_rol != 5 
                  ORDER BY u.nombre_completo ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarRol($id_usuario, $id_rol) {
        $query = "UPDATE " . $this->table_name . " SET id_rol = :id_rol WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol);
        $stmt->bindParam(':id_usuario', $id_usuario);
        return $stmt->execute();
    }

    public function eliminarUsuario($id_usuario) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        return $stmt->execute();
    }
}
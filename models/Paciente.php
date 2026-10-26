<?php
class Paciente {
    private $conn;
    private $table_name = "pacientes";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registra un nuevo paciente en la BD
    public function registrar($cedula, $nombre_completo, $fecha_nacimiento) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (cedula, nombre_completo, fecha_nacimiento) 
                      VALUES (:cedula, :nombre, :fecha)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombre', $nombre_completo);
            $stmt->bindParam(':fecha', $fecha_nacimiento);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            // Falla silenciosamente si la cédula está duplicada
            return false; 
        }
    }

    // Obtiene todos los pacientes para mostrarlos en la tabla
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id_paciente DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_paciente) {
        $query = "SELECT p.*, t.numero, t.tipo FROM " . $this->table_name . " p 
                  LEFT JOIN telefonos t ON p.id_paciente = t.id_paciente 
                  WHERE p.id_paciente = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_paciente);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Resuelve id_paciente a partir del número de expediente o de la cédula (coincidencia exacta en BD).
     */
    public function resolverIdPorIdOCedula($valor) {
        $v = trim((string) $valor);
        if ($v === '') {
            return null;
        }
        $stmt = $this->conn->prepare("SELECT id_paciente FROM " . $this->table_name . " WHERE cedula = ? LIMIT 1");
        $stmt->execute([$v]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return (int) $row['id_paciente'];
        }
        if (ctype_digit($v)) {
            $id = (int) $v;
            if ($id <= 0) {
                return null;
            }
            $stmt2 = $this->conn->prepare("SELECT id_paciente FROM " . $this->table_name . " WHERE id_paciente = ? LIMIT 1");
            $stmt2->execute([$id]);
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($row2) {
                return (int) $row2['id_paciente'];
            }
        }
        return null;
    }

    public function eliminar($id_paciente) {
        try {
            // Eliminar teléfonos primero
            $stmt1 = $this->conn->prepare("DELETE FROM telefonos WHERE id_paciente = :id");
            $stmt1->bindParam(':id', $id_paciente);
            $stmt1->execute();

            // Eliminar paciente
            $stmt2 = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id_paciente = :id");
            $stmt2->bindParam(':id', $id_paciente);
            return $stmt2->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function registrar_sin_usuario($cedula, $nombre_completo, $telefono, $tipo_telefono) {
        // Check if patient exists by cedula
        $check = $this->conn->prepare("SELECT id_paciente FROM " . $this->table_name . " WHERE cedula = :cedula");
        $check->bindParam(':cedula', $cedula);
        $check->execute();
        if ($row = $check->fetch(PDO::FETCH_ASSOC)) {
            $id_paciente = $row['id_paciente'];
            // Update or insert telefono
            $stmt_tel = $this->conn->prepare("INSERT INTO telefonos (id_paciente, numero, tipo) VALUES (:id, :numero, :tipo) 
                                             ON DUPLICATE KEY UPDATE numero = VALUES(numero), tipo = VALUES(tipo)");
            $stmt_tel->bindParam(':id', $id_paciente);
            $stmt_tel->bindParam(':numero', $telefono);
            $stmt_tel->bindParam(':tipo', $tipo_telefono);
            $stmt_tel->execute();
            return $id_paciente;
        }

        // New patient
        try {
            $this->conn->beginTransaction();

// Insertar paciente sin correo ni password (FIX: Added fecha_nacimiento required)
            $fecha_nacimiento = '1990-01-01'; // Dummy date for public citas
            $query = "INSERT INTO " . $this->table_name . " (cedula, nombre_completo, fecha_nacimiento) VALUES (:cedula, :nombre, :fecha_nac)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':cedula', $cedula);
            $stmt->bindParam(':nombre', $nombre_completo);
            $stmt->bindParam(':fecha_nac', $fecha_nacimiento);
            $stmt->execute();
            $id_paciente = $this->conn->lastInsertId();

            // Insertar teléfono
            $stmt2 = $this->conn->prepare("INSERT INTO telefonos (id_paciente, numero, tipo) VALUES (:id, :numero, :tipo)");
            $stmt2->bindParam(':id', $id_paciente);
            $stmt2->bindParam(':numero', $telefono);
            $stmt2->bindParam(':tipo', $tipo_telefono);
            $stmt2->execute();

            $this->conn->commit();
            return $id_paciente;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Paciente register_sin_usuario error for cedula {$cedula}: " . $e->getMessage());
            return false;
        }
    }
}
?>
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servicio.php';

class ServicioController {
    private $db;
    private $servicio;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->servicio = new Servicio($this->db);
    }

    /**
     * Vista de gestión de precios (Solo Administrador)
     */
    public function gestionar() {
        // Permitir a Administrador y Recepcionista ver la gestión de precios (solo Admin puede modificar)
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Administrador', 'Recepcionista'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        $servicios = $this->servicio->obtenerTodos();
        require_once __DIR__ . '/../views/admin/servicios/gestionar.php';
    }

    /**
     * Procesa la actualización de precios (Solo Administrador)
     */
    public function actualizar_precios() {
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        $precios = $_POST['precios'] ?? [];
        $errores = [];
        $actualizados = 0;

        // Si no se envió nada, redirigir sin intentar actualizar.
        if (empty($precios) || !is_array($precios)) {
            $_SESSION['error_msg'] = 'No se recibieron precios para actualizar.';
            header('Location: /cero_project/public/dashboard?tab=precios&error=1');
            exit();
        }

        // Para evitar fallos por valores inválidos o vacíos, recorremos únicamente los que tengan un id y valor.


        foreach ($precios as $id_servicio => $precio) {

            $id_servicio = (int) $id_servicio;
            $precio = floatval(str_replace(',', '.', $precio)); // Soportar coma decimal

            // Validación: precio debe ser positivo
            if ($precio < 0) {
                $errores[] = "El precio para el servicio ID {$id_servicio} no puede ser negativo.";
                continue;
            }

            // Validación: precio máximo razonable (evitar errores de digitación)
            if ($precio > 10000) {
                $errores[] = "El precio para el servicio ID {$id_servicio} excede el límite permitido ($10,000).";
                continue;
            }

            if ($this->servicio->actualizarPrecio($id_servicio, $precio)) {
                $actualizados++;
            } else {
                $errores[] = "Error al actualizar el servicio ID {$id_servicio}.";
            }
        }

        if (empty($errores)) {
            $_SESSION['success_msg'] = "{$actualizados} precios actualizados correctamente.";
            header('Location: /cero_project/public/dashboard?tab=precios&success=1');
        } else {
            $_SESSION['error_msg'] = implode(" | ", $errores);
            header('Location: /cero_project/public/dashboard?tab=precios&error=1');
        }

        exit();
    }
}
?>


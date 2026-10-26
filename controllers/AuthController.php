<?php
require_once '../config/database.php';
require_once '../models/Usuario.php';

class AuthController {
    private $db;
    private $usuario;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function login() {
        if (isset($_SESSION['id_usuario'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }

        $error = '';
        $info = isset($_GET['info']) ? trim($_GET['info']) : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
            $user_data = $this->usuario->verificarLogin($correo, $_POST['password']);
            
            if ($user_data) {
                session_regenerate_id(true);
                $_SESSION['id_usuario'] = $user_data['id_usuario'];
                $_SESSION['rol'] = $user_data['nombre_rol'];
                $_SESSION['nombre'] = $user_data['nombre_completo'];
                $_SESSION['correo'] = $correo; // Guardamos el correo para buscar su expediente
                
                header('Location: /cero_project/public/dashboard');
                exit();
            } else {
                $error = "Correo o contraseña inválidos.";
            }
        }
        require_once '../views/auth/login.php';
    }

    // ESTE ES EL MÉTODO QUE FALTABA Y CAUSABA EL ERROR FATAL
    public function registro() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
                
                $cedula = trim($_POST['cedula']);
                if (!ctype_digit($cedula)) {
                    throw new Exception("el campo cedula solamente registra numeros");
                }

                // 1. Crear credenciales de acceso (Rol 5 = Paciente)
                $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $stmt1 = $this->db->prepare("INSERT INTO usuarios (id_rol, nombre_completo, correo, password_hash) VALUES (5, ?, ?, ?)");
                $stmt1->execute([$_POST['nombre_completo'], $_POST['correo'], $hash]);

                // 2. Crear Expediente Médico
                $stmt2 = $this->db->prepare("INSERT INTO pacientes (cedula, nombre_completo, fecha_nacimiento, correo, password_hash) VALUES (?, ?, ?, ?, ?)");
                $stmt2->execute([$cedula, $_POST['nombre_completo'], $_POST['fecha_nacimiento'], $_POST['correo'], $hash]);
                $id_paciente = $this->db->lastInsertId();

                // 3. Registrar Teléfono
                $stmt3 = $this->db->prepare("INSERT INTO telefonos (id_paciente, numero, tipo) VALUES (?, ?, ?)");
                $stmt3->execute([$id_paciente, $_POST['numero'], $_POST['tipo_telefono']]);

                $this->db->commit();
                echo "<script>alert('¡Expediente creado! Por favor inicia sesión.'); window.location.href='/cero_project/public/login';</script>";
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                $error = $e->getMessage() ?: "El correo o la cédula ya están registrados.";
            }
        }
        require_once '../views/auth/registro.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /cero_project/public/');
        exit();
    }

    public function crear_empleado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['rol']) && $_SESSION['rol'] == 'Administrador') {
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $password = $_POST['password'];
            $id_rol = (int)$_POST['id_rol'];

            try {
                if ($this->usuario->registrarPersonal($nombre, $correo, $password, $id_rol)) {
                    header('Location: /cero_project/public/dashboard?success=1');
                    exit();
                } else {
                    header('Location: /cero_project/public/dashboard?error=Inserción fallida');
                    exit();
                }
            } catch (Exception $e) {
                header('Location: /cero_project/public/dashboard?error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
    }

    public function setup() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                // Registrar administrador inicial
                $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $stmt = $this->db->prepare("INSERT INTO usuarios (id_rol, nombre_completo, correo, password_hash) VALUES (1, ?, ?, ?)");
                $stmt->execute([$_POST['nombre'], $_POST['correo'], $hash]);

                $this->db->commit();
                echo "<script>alert('Sistema inicializado. Ahora puedes iniciar sesión.'); window.location.href='/cero_project/public/login';</script>";
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                $error = "Error al inicializar: " . $e->getMessage();
            }
        }
        require_once '../views/auth/setup.php';
    }

    public function actualizar_rol() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['rol']) && $_SESSION['rol'] == 'Administrador') {
            $id_usuario = (int)$_POST['id_usuario'];
            $id_rol = (int)$_POST['id_rol'];

            try {
                if ($this->usuario->actualizarRol($id_usuario, $id_rol)) {
                    header('Location: /cero_project/public/dashboard?success=2');
                    exit();
                } else {
                    header('Location: /cero_project/public/dashboard?error=Actualización fallida');
                    exit();
                }
            } catch (Exception $e) {
                header('Location: /cero_project/public/dashboard?error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
    }

    public function eliminar_empleado() {
        if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_SESSION['rol']) && $_SESSION['rol'] == 'Administrador') {
            $id_usuario = (int)$_GET['id'];

            try {
                if ($this->usuario->eliminarUsuario($id_usuario)) {
                    header('Location: /cero_project/public/dashboard?success=3');
                    exit();
                } else {
                    header('Location: /cero_project/public/dashboard?error=Eliminación fallida');
                    exit();
                }
            } catch (Exception $e) {
                header('Location: /cero_project/public/dashboard?error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
    }
}
?>
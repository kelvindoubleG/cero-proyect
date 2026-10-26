<?php
session_start();

// Configuración de rutas
$request = $_SERVER['REQUEST_URI'];
$base_path = '/cero_project/public';
$route = str_replace($base_path, '', $request);
$route = strtok($route, '?'); 

// Rutas accesibles sin sesión (landing, auth y solicitud de cita desde la web pública).
$rutas_publicas = ['', '/', '/login', '/registro', '/setup', '/cita/publica'];

// SEGURIDAD: Si no hay sesión y la ruta es privada, al login.
if (!isset($_SESSION['id_usuario']) && !in_array($route, $rutas_publicas)) {
    header('Location: ' . $base_path . '/login');
    exit();
}

switch ($route) {
    case '':
    case '/':
        require_once '../controllers/PublicController.php';
        (new PublicController())->index();
        break;
    case '/login':
        require_once '../controllers/AuthController.php';
        (new AuthController())->login();
        break;
    case '/registro':
        require_once '../controllers/AuthController.php';
        (new AuthController())->registro();
        break;
    case '/setup':
        require_once '../controllers/AuthController.php';
        (new AuthController())->setup();
        break;
    case '/dashboard':
        require_once '../controllers/DashboardController.php';
        (new DashboardController())->index();
        break;
    case '/pacientes':
        header('Location: ' . $base_path . '/dashboard?tab=pacientes');
        exit();
        break;
    case '/evolucion':
        header('Location: ' . $base_path . '/dashboard?tab=evolucion');
        exit();
        break;
    case '/kpi':
        header('Location: ' . $base_path . '/dashboard?tab=kpi');
        exit();
        break;
    case '/recuperacion':
        header('Location: ' . $base_path . '/dashboard?tab=recuperacion');
        exit();
        break;
    case '/admin/crear_empleado':
        require_once '../controllers/AuthController.php';
        (new AuthController())->crear_empleado();
        break;
    case '/admin/actualizar_rol':
        require_once '../controllers/AuthController.php';
        (new AuthController())->actualizar_rol();
        break;
    case '/admin/eliminar_empleado':
        require_once '../controllers/AuthController.php';
        (new AuthController())->eliminar_empleado();
        break;

    case '/admin/servicios':
        require_once '../controllers/ServicioController.php';
        (new ServicioController())->gestionar();
        break;
    case '/admin/servicios/actualizar':
        require_once '../controllers/ServicioController.php';
        (new ServicioController())->actualizar_precios();
        break;
    case '/admin/ver_paciente':
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        require_once '../controllers/PacienteController.php';
        (new PacienteController())->ver($_GET['id']);
        break;
    case '/admin/eliminar_paciente':
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: /cero_project/public/dashboard');
            exit();
        }
        require_once '../controllers/PacienteController.php';
        (new PacienteController())->eliminar($_GET['id']);
        break;
    case '/cita/publica':
        require_once '../controllers/CitaController.php';
        (new CitaController())->cita_publica();
        break;
    case '/cita/registrar':
        require_once '../controllers/CitaController.php';
        (new CitaController())->registrar();
        break;
    case '/citas/agendar':
        require_once '../controllers/CitaController.php';
        (new CitaController())->agendar();
        break;
    case '/evolucion/registrar':
        require_once '../controllers/EvolucionController.php';
        (new EvolucionController())->registrar();
        break;
    case '/nutricion/registrar':
        require_once __DIR__ . '/../controllers/NutricionController.php';
        (new NutricionController())->registrar();
        break;
    case '/entrenamiento/registrar':
        require_once '../controllers/EntrenamientoController.php';
        (new EntrenamientoController())->registrar();
        break;
    case '/entrenamiento/actualizar_estado':
        require_once '../controllers/EntrenamientoController.php';
        (new EntrenamientoController())->actualizar_estado();
        break;
    case '/entrenamiento/eliminar':
        require_once '../controllers/EntrenamientoController.php';
        (new EntrenamientoController())->eliminar();
        break;
    case '/kpi/registrar':
        require_once '../controllers/KPIController.php';
        (new KPIController())->registrar();
        break;
    case '/recuperacion/registrar':
        require_once __DIR__ . '/../controllers/RecuperacionController.php';
        (new RecuperacionController())->registrar();
        break;
    case '/logout':
        session_destroy();
        header('Location: ' . $base_path . '/');
        exit();
    case '/clear_paciente':
        // Limpia la selección de paciente en sesión
        if (isset($_SESSION['paciente_id'])) {
            unset($_SESSION['paciente_id']);
        }
        if (isset($_SESSION['paciente_nombre'])) {
            unset($_SESSION['paciente_nombre']);
        }
        header('Location: ' . $base_path . '/dashboard');
        exit();
     case '/citas/actualizar_estado':   
        require_once '../controllers/CitaController.php';
        (new CitaController())->actualizar_estado();
        break;
    case '/citas/eliminar':
        require_once '../controllers/CitaController.php';
        (new CitaController())->eliminar();
        break;
    // ------------------------------------------------------------------
    // Modulo Contable
    // ------------------------------------------------------------------
    case '/contabilidad':
        require_once '../controllers/ContabilidadController.php';
        (new ContabilidadController())->index();
        break;
    case '/contabilidad/asiento/crear':
        require_once '../controllers/ContabilidadController.php';
        (new ContabilidadController())->crearAsiento();
        break;
    case '/contabilidad/periodo/abrir':
        require_once '../controllers/ContabilidadController.php';
        (new ContabilidadController())->abrirPeriodo();
        break;
    case '/contabilidad/libro-diario':
        require_once '../controllers/ContabilidadController.php';
        (new ContabilidadController())->libroDiario();
        break;

    // ------------------------------------------------------------------
    // APIs JSON
    // ------------------------------------------------------------------
    case '/api/bcv':
        require_once '../controllers/BcvController.php';
        (new BcvController())->api();
        break;
    case '/api/plan-cuentas':
        require_once '../controllers/ContabilidadController.php';
        (new ContabilidadController())->planCuentas();
        break;

    default:
        http_response_code(404);
        include '../views/404.php';
        break;
}
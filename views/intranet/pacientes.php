<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CERO | Gestión de Pacientes</title>
    <link rel="stylesheet" href="/cero_project/public/css/style.css">
</head>
<body class="dashboard-layout">

    <aside>
        <div class="logo-container">
            <h1>CE<span>RO</span></h1>
            <p class="subtitle">Alto Rendimiento</p>
        </div>
        <nav>
            <a href="/cero_project/public/dashboard" style="text-decoration:none;">
                <button class="nav-btn">Dashboard</button>
            </a>
            <a href="/cero_project/public/pacientes" style="text-decoration:none;">
                <button class="nav-btn active">Gestión de Pacientes</button>
            </a>
        </nav>
        <div class="user-info">
            <p><strong>Rol:</strong> <?= $_SESSION['rol']; ?></p>
            <p><?= $_SESSION['nombre']; ?></p>
        </div>
    </aside>

    <main>
        <header>
            <h2>Expediente Clínico-Deportivo</h2>
            <a href="/cero_project/public/logout"><button class="btn-logout">Cerrar Sesión</button></a>
        </header>

        <div class="content-area">
            <section class="section-module active">
                
                <?php if(isset($_GET['msg']) && $_GET['msg'] == 'exito'): ?>
                    <p style="color: #a16207; font-weight:bold; margin-bottom:15px;">¡Paciente registrado correctamente!</p>
                <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'error'): ?>
                    <p style="color: #854d0e; font-weight:bold; margin-bottom:15px;">Error: La cédula ya se encuentra registrada.</p>
                <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'error_numerico'): ?>
                    <p style="color: #ef4444; font-weight:bold; margin-bottom:15px;">Error: el campo cedula solamente registra numeros</p>
                <?php endif; ?>

                <div class="form-container">
                    <h3>Registrar Nuevo Atleta</h3><br>
                    <form action="/cero_project/public/pacientes/registrar" method="POST">
                        <div class="grid-cards margin-none">
                            <div class="form-group">
                                <label>Cédula de identidad:</label>
                                <input type="text" name="cedula" required placeholder="Ej: 30123456" pattern="\d+" inputmode="numeric" oninput="this.setCustomValidity(''); this.value = this.value.replace(/[^0-9]/g, '')" oninvalid="this.setCustomValidity('el campo cedula solamente registra numeros')">
                            </div>
                            <div class="form-group">
                                <label>Nombre completo:</label>
                                <input type="text" name="nombre_completo" required placeholder="Nombres y Apellidos">
                            </div>
                            <div class="form-group">
                                <label>Fecha de nacimiento:</label>
                                <input type="date" name="fecha_nacimiento" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit mt-15">Guardar Expediente</button>
                    </form>
                </div>

                <hr>
                
                <h3>Directorio de Pacientes</h3><br>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($lista_pacientes)): ?>
                            <tr><td colspan="5" style="text-align:center;">No hay pacientes registrados aún.</td></tr>
                        <?php else: ?>
                            <?php foreach($lista_pacientes as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['id_paciente']) ?></td>
                                    <td><?= htmlspecialchars($p['cedula']) ?></td>
                                    <td><?= htmlspecialchars($p['nombre_completo']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_nacimiento'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['fecha_registro'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

</body>
</html>
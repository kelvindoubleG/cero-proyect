<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CERO | Intranet Personal</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: ceroTabIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) both; }
        @keyframes ceroTabIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        /* Expediente (Fisio/Nutri): logo en menú — clipboard + cruz (SVG data-uri) */
        #btn-evolucion {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 0.65rem !important;
            width: 100% !important;
            text-align: left !important;
        }
        #btn-evolucion::before {
            content: "";
            flex-shrink: 0;
            width: 1.35rem;
            height: 1.35rem;
            background: center / contain no-repeat url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23f5f5f4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h2'/%3E%3Cpath d='M15 5h2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-2'/%3E%3Crect width='8' height='4' x='8' y='2' rx='1'/%3E%3Cpath d='M12 11v4'/%3E%3Cpath d='M10 13h4'/%3E%3C/svg%3E");
            opacity: 0.95;
        }
        #btn-evolucion:hover::before {
            filter: brightness(1.12);
        }
        #btn-evolucion.bg-cero-sun::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230a0a0a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h2'/%3E%3Cpath d='M15 5h2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-2'/%3E%3Crect width='8' height='4' x='8' y='2' rx='1'/%3E%3Cpath d='M12 11v4'/%3E%3Cpath d='M10 13h4'/%3E%3C/svg%3E");
            filter: none;
            opacity: 1;
        }
        #btn-evolucion > [data-lucide] {
            display: none !important;
        }

        .detail-row td {
            padding: 0;
            border-top: none;
        }
        .detail-panel {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.28s ease, opacity 0.24s ease, padding 0.28s ease;
        }
        .detail-row.open .detail-panel {
            max-height: 1200px;
            opacity: 1;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .toggle-plan-detail .chevron-icon {
            display: inline-block;
            transform: rotate(0deg);
            transition: transform 0.2s ease;
        }
        .toggle-plan-detail[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-cero-paper flex h-screen overflow-hidden text-cero-ink font-sans">

   <aside class="w-72 bg-gradient-to-b from-cero-black via-cero-black to-cero-ink text-cero-mist flex flex-col shadow-xl z-20 border-r border-cero-line">
        <div class="p-6 text-center border-b border-cero-line/80 bg-cero-ink/50 backdrop-blur-sm">
            <div class="flex items-center justify-center gap-2 mb-1">
                <i data-lucide="activity" class="w-6 h-6 text-cero-sun"></i>
                <h1 class="text-3xl font-extrabold tracking-widest text-white">CE<span class="text-cero-sun">RO</span></h1>
            </div>
            <p class="text-xs font-bold text-cero-soft mt-1 uppercase tracking-wider">Alto Rendimiento</p>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scroll-smooth">
            <button type="button" onclick="switchTab('resumen')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold bg-cero-sun text-cero-black shadow-md border-l-4 border-cero-amber nav-btn transition-all duration-200 ease-cero-out hover:brightness-[1.06]" id="btn-resumen">
                <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i>
                <span>Dashboard Central</span>
            </button>
            
            <?php if($_SESSION['rol'] == 'Administrador' || $_SESSION['rol'] == 'Recepcionista'): ?>
                <div class="pt-4 pb-2"><p class="text-xs font-bold text-cero-muted uppercase tracking-wider px-4">Panel Gerencial</p></div>
                <button type="button" onclick="switchTab('precios')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-precios">
                    <i data-lucide="dollar-sign" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Gestión de Precios</span>
                </button>
                <?php if($_SESSION['rol'] == 'Administrador'): ?>
                    <button type="button" onclick="switchTab('usuarios')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-usuarios">
                        <i data-lucide="shield-check" class="w-5 h-5 shrink-0 opacity-90"></i>
                        <span>Gestión de Empleados</span>
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($_SESSION['rol'] == 'Administrador' || $_SESSION['rol'] == 'Recepcionista'): ?>
                <button type="button" onclick="switchTab('pacientes')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-pacientes">
                    <i data-lucide="users-round" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Directorio Pacientes</span>
                </button>
                <button type="button" onclick="switchTab('citas')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-citas">
                    <i data-lucide="calendar-range" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Gestión de Citas</span>
                </button>
            <?php endif; ?>

            <?php if($_SESSION['rol'] == 'Administrador' || $_SESSION['rol'] == 'Especialista'): ?>
                <div class="pt-4 pb-2"><p class="text-xs font-bold text-cero-muted uppercase tracking-wider px-4">Área Médica</p></div>
                <button type="button" onclick="switchTab('evolucion')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-evolucion">
                    <i data-lucide="clipboard-pulse" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Expediente (Fisio/Nutri)</span>
                </button>
            <?php endif; ?>

            <?php if($_SESSION['rol'] == 'Administrador' || $_SESSION['rol'] == 'Entrenador'): ?>
                <button type="button" onclick="switchTab('kpi')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-kpi">
                    <i data-lucide="trending-up" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Indicadores KPI</span>
                </button>
                <button type="button" onclick="switchTab('recuperacion')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-recuperacion">
                    <i data-lucide="snowflake" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Crioterapia y Sauna</span>
                </button>
            <?php endif; ?>

            <?php if($_SESSION['rol'] == 'Administrador'): ?>
                <div class="pt-4 pb-2"><p class="text-xs font-bold text-cero-muted uppercase tracking-wider px-4">Inteligencia</p></div>
                <button type="button" onclick="switchTab('inteligencia')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-inteligencia">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Tablero de Inteligencia</span>
                </button>
                <button type="button" onclick="switchTab('reportes')" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 ease-cero-out nav-btn" id="btn-reportes">
                    <i data-lucide="line-chart" class="w-5 h-5 shrink-0 opacity-90"></i>
                    <span>Módulo de Reportes</span>
                </button>
            <?php endif; ?>

            <a href="/cero_project/public/" class="mt-6 flex items-center justify-center gap-2 w-full text-center px-4 py-3 rounded-xl font-bold text-cero-black bg-cero-sun hover:bg-cero-gold transition-all duration-200 shadow-md hover:shadow-cero-glow border border-cero-amber/30">
                <i data-lucide="arrow-left" class="w-4 h-4 shrink-0"></i>
                <span>Web principal</span>
            </a>
        </nav>

        <div class="p-4 bg-cero-elevated/90 border-t border-cero-line backdrop-blur-sm">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="shield-check" class="w-4 h-4 text-cero-sun shrink-0"></i>
                <p class="text-xs text-cero-soft font-bold">Rol: <?= htmlspecialchars($_SESSION['rol']) ?></p>
            </div>
            <p class="font-bold text-sm truncate text-white pl-6"><?= htmlspecialchars($_SESSION['nombre']) ?></p>
            <?php if (isset($_SESSION['paciente_id'])): ?>
                <div class="mt-3 pl-6">
                    <p class="text-xs text-cero-muted">Paciente seleccionado</p>
                    <p class="font-bold text-sm text-white truncate"><?= htmlspecialchars($_SESSION['paciente_nombre'] ?? 'N/D') ?> — ID <?= htmlspecialchars((string) $_SESSION['paciente_id']) ?></p>
                    <a href="/cero_project/public/clear_paciente" class="text-xs text-cero-amber hover:underline">Cambiar paciente</a>
                </div>
            <?php endif; ?>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen relative">
        <div id="dashboardLoader">
            <div class="flex flex-col items-center gap-4">
                <div class="loader-ring"></div>
                <p class="text-sm font-semibold">Cargando Panel Central...</p>
            </div>
        </div>
        <header class="bg-white/90 backdrop-blur-md h-20 px-8 flex items-center justify-between shadow-sm z-10 border-b border-cero-fog supports-[backdrop-filter]:bg-white/80">
            <div class="flex items-center gap-3">
                <span class="hidden sm:flex h-10 w-1 rounded-full bg-gradient-to-b from-cero-amber to-cero-sun shadow-sm"></span>
                <h2 class="text-xl font-bold text-cero-ink tracking-tight">Panel de Control de la Clínica</h2>
            </div>
            <a href="/cero_project/public/logout" class="inline-flex items-center gap-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm border border-red-800/30 hover:border-red-500">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Cerrar sesión</span>
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-8 bg-cero-cream/40 scroll-smooth">
            
            


            <section id="resumen" class="tab-content active max-w-6xl mx-auto animate-cero-fade-up">

                <?php if (!empty($dashboardError)): ?>
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm font-semibold text-red-800 shadow-sm">
                        <?= htmlspecialchars($dashboardError) ?>
                    </div>
                <?php endif; ?>

                <h2 class="text-3xl font-extrabold text-cero-black mb-6 tracking-tight">Resumen del Sistema</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="group relative bg-white p-6 rounded-2xl shadow-sm border border-cero-fog/80 border-l-4 border-l-cero-amber overflow-hidden transition-all duration-300 ease-cero-out hover:-translate-y-1 hover:shadow-cero hover:border-cero-amber/30 animate-cero-fade-up">
                        <i data-lucide="users-round" class="absolute right-4 top-4 w-14 h-14 text-cero-sun/25 pointer-events-none transition-transform duration-300 group-hover:scale-110 group-hover:text-cero-amber/20"></i>
                        <h3 class="text-sm font-bold text-cero-muted relative">Pacientes Activos</h3>
                        <p class="text-3xl font-extrabold mt-2 relative text-cero-black"><?= $stats['pacientes'] ?? 0 ?></p>
                    </div>
                    <div class="group relative bg-white p-6 rounded-2xl shadow-sm border border-cero-fog/80 border-l-4 border-l-cero-amber overflow-hidden transition-all duration-300 ease-cero-out hover:-translate-y-1 hover:shadow-cero hover:border-cero-amber/30 animate-cero-fade-up [animation-delay:75ms]">
                        <i data-lucide="calendar-clock" class="absolute right-4 top-4 w-14 h-14 text-cero-sun/25 pointer-events-none transition-transform duration-300 group-hover:scale-110 group-hover:text-cero-amber/20"></i>
                        <h3 class="text-sm font-bold text-cero-muted relative">Citas pendientes</h3>
                        <p class="text-3xl font-extrabold mt-2 relative text-cero-honey"><?= $stats['citas_hoy'] ?? 0 ?></p>
                    </div>
                    <div class="group relative bg-white p-6 rounded-2xl shadow-sm border border-cero-fog/80 border-l-4 border-l-cero-amber overflow-hidden transition-all duration-300 ease-cero-out hover:-translate-y-1 hover:shadow-cero hover:border-cero-amber/30 animate-cero-fade-up [animation-delay:150ms]">
                        <i data-lucide="snowflake" class="absolute right-4 top-4 w-14 h-14 text-cero-sun/25 pointer-events-none transition-transform duration-300 group-hover:scale-110 group-hover:text-cero-amber/20"></i>
                        <h3 class="text-sm font-bold text-cero-muted relative">Sesiones Crioterapia</h3>
                        <p class="text-3xl font-extrabold mt-2 relative text-cero-amber"><?= $stats['crioterapia'] ?? 0 ?></p>
                    </div>
                </div>

                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                    <!-- Mini-gráficas centrales solicitadas: tendencia, estado de citas, probabilidad retorno -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-cero-fog text-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-sm text-cero-ink">Tendencia de nuevos pacientes</h4>
                                    <p class="text-xs text-cero-muted">Nuevas altas (últimos períodos)</p>
                                </div>
                            </div>
                            <canvas id="mini-tendencia-pacientes" height="140" style="height:140px;max-height:140px;"></canvas>
                        </div>

                        <div class="bg-white p-4 rounded-xl shadow-sm border border-cero-fog text-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-sm text-cero-ink">Distribución estado de citas</h4>
                                    <p class="text-xs text-cero-muted">Proporciones por estado</p>
                                </div>
                            </div>
                            <canvas id="mini-estado-citas" height="140" style="height:140px;max-height:140px;"></canvas>
                        </div>

                        <div class="bg-white p-4 rounded-xl shadow-sm border border-cero-fog text-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-semibold text-sm text-cero-ink">Probabilidad retorno de paciente</h4>
                                    <p class="text-xs text-cero-muted">Porcentaje estimado</p>
                                </div>
                            </div>
                            <canvas id="mini-prob-retorno" height="140" style="height:140px;max-height:140px;"></canvas>
                        </div>
                    </div>

                    <script>
                        (function() {
                            // Datos desde PHP para las mini-gráficas
                            window.resumenMiniDashboard = {
                                tendenciaLabels: <?= json_encode(array_column($pacientes_tendencia ?? [], 'periodo'), JSON_UNESCAPED_UNICODE) ?>,
                                tendenciaData: <?= json_encode(array_map('intval', array_column($pacientes_tendencia ?? [], 'total'))) ?>,
                                estadoLabels: <?= json_encode(array_column($estado_citas_dist ?? [], 'estado_cita'), JSON_UNESCAPED_UNICODE) ?>,
                                estadoData: <?= json_encode(array_map('intval', array_column($estado_citas_dist ?? [], 'total'))) ?>,
                                probRetorno: <?= json_encode(isset($probabilidad_retorno_paciente) ? (float)$probabilidad_retorno_paciente : 0) ?>
                            };

                            function renderMiniCharts() {
                                if (typeof Chart === 'undefined' || !window.resumenMiniDashboard) return;

                                const miniOptions = {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false }, tooltip: { enabled: true, bodyFont: { size: 12 }, titleFont: { size: 12 } } },
                                    scales: { x: { display: true, ticks: { font: { size: 11 } }, grid: { display: false } }, y: { display: true, ticks: { font: { size: 11 } }, grid: { display: false } } },
                                    elements: { bar: { borderRadius: 6, barThickness: 16 }, point: { radius: 3 } }
                                };

                                // Tendencia (línea)
                                try {
                                    const tCtx = document.getElementById('mini-tendencia-pacientes');
                                    if (tCtx && window.resumenMiniDashboard.tendenciaLabels.length) {
                                        try { const prev = Chart.getChart(tCtx); if (prev) prev.destroy(); } catch(e){}
                                        new Chart(tCtx, { type: 'line', data: { labels: window.resumenMiniDashboard.tendenciaLabels, datasets: [{ label: 'Nuevos pacientes', data: window.resumenMiniDashboard.tendenciaData, fill: true, backgroundColor: 'rgba(59,130,246,0.12)', borderColor: 'rgba(59,130,246,1)', tension: 0.35 }] }, options: miniOptions });
                                    }
                                } catch(e){ console.error('mini-tendencia error', e); }

                                // Estado (doughnut)
                                try {
                                    const eCtx = document.getElementById('mini-estado-citas');
                                    if (eCtx && window.resumenMiniDashboard.estadoLabels.length) {
                                        try { const prev = Chart.getChart(eCtx); if (prev) prev.destroy(); } catch(e){}
                                        new Chart(eCtx, { type: 'doughnut', data: { labels: window.resumenMiniDashboard.estadoLabels, datasets: [{ data: window.resumenMiniDashboard.estadoData, backgroundColor: ['#fde047','#fb923c','#4ade80','#60a5fa','#a855f7'] }] }, options: Object.assign({}, miniOptions, { plugins: { legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, boxWidth: 10 } }, tooltip: { enabled: true } } }) });
                                    }
                                } catch(e){ console.error('mini-estado error', e); }

                                // Probabilidad retorno (doughnut simple)
                                try {
                                    const pCtx = document.getElementById('mini-prob-retorno');
                                    if (pCtx) {
                                        const p = Math.max(0, Math.min(100, Number(window.resumenMiniDashboard.probRetorno || 0)));
                                        try { const prev = Chart.getChart(pCtx); if (prev) prev.destroy(); } catch(e){}
                                        new Chart(pCtx, { type: 'doughnut', data: { labels: ['Retorno','No retorno'], datasets: [{ data: [p, 100 - p], backgroundColor: ['#10b981','#e5e7eb'] }] }, options: Object.assign({}, miniOptions, { cutout: '70%', plugins: { legend: { display: false }, tooltip: { enabled: true } }, elements: { center: { } } }) });
                                    }
                                } catch(e){ console.error('mini-prob error', e); }
                            }

                            document.addEventListener('DOMContentLoaded', renderMiniCharts);
                        })();
                    </script>
                <?php endif; ?>

                <?php if($_SESSION['rol'] == 'Administrador' && !empty($empleados)): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                        <h3 class="text-2xl font-bold text-cero-black mb-4 flex items-center gap-2">
                            <i data-lucide="id-card" class="w-6 h-6 text-cero-honey"></i>
                            Empleados Registrados
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-cero-wheat text-cero-ink">
                                        <th class="p-4 font-bold">Nombre</th>
                                        <th class="p-4 font-bold">Rol</th>
                                        <th class="p-4 font-bold">Correo</th>
                                        <th class="p-4 font-bold">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <tr class="border-b border-cero-fog hover:bg-cero-cream/40">
                                            <td class="p-4 font-bold"><?= htmlspecialchars($empleado['nombre_completo']) ?></td>
                                            <td class="p-4">
                                                <form action="/cero_project/public/admin/actualizar_rol" method="POST" style="display: inline;">
                                                    <input type="hidden" name="id_usuario" value="<?= $empleado['id_usuario'] ?>">
                                                    <select name="id_rol" class="px-2 py-1 border rounded" onchange="this.form.submit()">
                                                        <option value="1" <?= $empleado['id_rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                                        <option value="2" <?= $empleado['id_rol'] == 2 ? 'selected' : '' ?>>Especialista</option>
                                                        <option value="3" <?= $empleado['id_rol'] == 3 ? 'selected' : '' ?>>Entrenador</option>
                                                        <option value="4" <?= $empleado['id_rol'] == 4 ? 'selected' : '' ?>>Recepcionista</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="p-4"><?= htmlspecialchars($empleado['correo']) ?></td>
                                            <td class="p-4">
                                                <a href="/cero_project/public/admin/eliminar_empleado?id=<?= $empleado['id_usuario'] ?>" onclick="return confirm('¿Estás seguro de eliminar este empleado?')" class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-red-700 font-bold transition-all duration-200 shadow-sm border border-red-800/20">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
    <div class="mt-8 pt-8 border-t-2 border-cero-fog space-y-8">
        <div class="flex items-center gap-2">
            <span class="bg-cero-honey text-white text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">Sección Gerencial Privada</span>
            <h3 class="text-2xl font-extrabold text-cero-black tracking-tight">Módulo Estratégico y de Rentabilidad</h3>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-cero-line">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-cero-fog pb-4 mb-4">
                <div>
                        <h4 class="text-lg font-bold text-cero-black flex items-center gap-2">
                        <i data-lucide="dollar-sign" class="w-5 h-5 text-cero-honey"></i>
                        Consolidado financiero de ingresos por expedientes
                    </h4>
                    <p class="text-xs text-cero-muted mt-0.5">Ingresos brutos calculados sobre servicios completados o confirmados.</p>
                </div>
                <button type="button" onclick="exportarReporteAPDF('gerencial-finanzas', 'Reporte_Financiero_CERO')" class="inline-flex items-center gap-1.5 bg-cero-sun hover:bg-cero-gold text-cero-black font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-sm border border-cero-amber/30">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Exportar a PDF
                </button>
            </div>
            <div id="gerencial-finanzas" class="overflow-x-auto">
                    <div class="hidden-print-header hidden pb-4 mb-4 border-b border-cero-line">
                    <h2 class="text-xl font-black">Centro de Alto Rendimiento CERO</h2>
                    <p class="text-xs text-cero-muted">Consolidado financiero de ingresos por expedientes — Fecha: <?= date('d/m/Y') ?></p>
                </div>
                <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($_GET['tab'] ?? 'resumen') ?>">
                    <input type="hidden" name="subtab" value="">
                    <div class="flex-1">
                        <label for="fecha_inicio_rep9" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                        <input id="fecha_inicio_rep9" name="fecha_inicio_rep9" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep9'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                    </div>
                    <div class="flex-1">
                        <label for="fecha_fin_rep9" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                        <input id="fecha_fin_rep9" name="fecha_fin_rep9" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep9'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                    </div>
                    <div class="flex-none">
                        <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                        <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                    </div>
                </form>
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-cero-wheat text-cero-ink border-b border-cero-fog">
                        <tr>
                            <th class="p-3 font-bold">Servicio Prestado</th>
                            <th class="p-3 font-bold text-center">Volumen Atendido</th>
                            <th class="p-3 font-bold text-right">Ingresos Proyectados (USD)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cero-fog/60">
                        <?php
                        if (isset($reporte_financiero)) {
                                $filas = $reporte_financiero;
                                $total_usd = 0;
                                if (!empty($filas)) {
                                    $labels = [];
                                    $vals = [];
                                    foreach ($filas as $f) {
                                        $total_usd += $f['ingresos'];
                                        $labels[] = $f['nombre_servicio'];
                                        $vals[] = (float)$f['ingresos'];
                                        echo "<tr class='hover:bg-cero-cream/40'>";
                                        echo "<td class='p-3 font-bold text-cero-black'>" . htmlspecialchars($f['nombre_servicio']) . "</td>";
                                        echo "<td class='p-3 text-center font-mono'>" . htmlspecialchars($f['volumen']) . "</td>";
                                        echo "<td class='p-3 text-right font-mono font-bold text-green-700'>$" . number_format($f['ingresos'], 2) . "</td>";
                                        echo "</tr>";
                                    }
                                    echo "<tr class='bg-cero-paper font-black text-sm border-t-2 border-cero-line'>";
                                    echo "<td class='p-3'>Total General Proyectado:</td><td class='p-3'></td>";
                                    echo "<td class='p-3 text-right font-mono text-green-800'>$" . number_format($total_usd, 2) . "</td>";
                                    echo "</tr>";
                                    echo "</tbody></table>\n";
                                    echo "<script>window.rep9_labels = " . json_encode($labels, JSON_UNESCAPED_UNICODE) . "; window.rep9_vals = " . json_encode($vals) . ";</script>";
                                } else {
                                    echo "<tr><td colspan='3' class='p-4 text-center text-cero-muted'>No hay ingresos registrados para mostrar.</td></tr>";
                                }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-cero-line">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-cero-fog pb-4 mb-4">
                <div>
                    <h4 class="text-lg font-bold text-cero-black flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-cero-honey"></i>
                        Prevalencia Patologías Biomecánicas
                    </h4>
                    <p class="text-xs text-cero-muted mt-0.5">Diagnósticos biomecánicos más frecuentes tratados en el centro.</p>
                </div>
                <button type="button" onclick="exportarReporteAPDF('gerencial-patologias', 'Prevalencia_Patologias_CERO')" class="inline-flex items-center gap-1.5 bg-cero-sun hover:bg-cero-gold text-cero-black font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-sm border border-cero-amber/30">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Exportar a PDF
                </button>
            </div>
            <div id="gerencial-patologias" class="overflow-x-auto">
                <div class="hidden-print-header hidden pb-4 mb-4 border-b border-cero-line">
                    <h2 class="text-xl font-black">Centro de Alto Rendimiento CERO</h2>
                    <p class="text-xs text-cero-muted">Reporte Gerencial de Patologías Recurrentes — Fecha: <?= date('d/m/Y') ?></p>
                </div>
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-cero-wheat text-cero-ink border-b border-cero-fog">
                        <tr>
                            <th class="p-3 font-bold">Diagnóstico Clínico Registrado</th>
                            <th class="p-3 font-bold text-center">Frecuencia (Casos Tratados)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cero-fog/60">
                        <?php
                        if (isset($reporte_patologias)) {
                            if (!empty($reporte_patologias)) {
                                foreach ($reporte_patologias as $f) {
                                    echo "<tr class='hover:bg-cero-cream/40'>";
                                    echo "<td class='p-3 font-bold text-cero-black'>" . htmlspecialchars($f['diagnostico']) . "</td>";
                                    echo "<td class='p-3 text-center font-mono font-semibold'>" . htmlspecialchars($f['casos']) . " casos</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2' class='p-4 text-center text-cero-muted'>No hay diagnósticos cargados en el sistema.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-cero-line">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-cero-fog pb-4 mb-4">
                <div>
                    <h4 class="text-lg font-bold text-cero-black flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-5 h-5 text-cero-honey"></i>
                        Servicios Más Demandados
                    </h4>
                    <p class="text-xs text-cero-muted mt-0.5">Consulta los servicios más solicitados para identificar la demanda del centro.</p>
                </div>
                <button type="button" onclick="exportarReporteAPDF('gerencial-personal', 'Servicios_Mas_Demandados_CERO')" class="inline-flex items-center gap-1.5 bg-cero-sun hover:bg-cero-gold text-cero-black font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-sm border border-cero-amber/30">
                    <i data-lucide="file-text" class="w-4 h-4"></i> Exportar a PDF
                </button>
            </div>
            <div id="gerencial-personal" class="overflow-x-auto">
                <div class="hidden-print-header hidden pb-4 mb-4 border-b border-cero-line">
                    <h2 class="text-xl font-black">Centro de Alto Rendimiento CERO</h2>
                    <p class="text-xs text-cero-muted">Reporte Gerencial de Servicios Más Demandados — Fecha: <?= date('d/m/Y') ?></p>
                </div>
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-cero-wheat text-cero-ink border-b border-cero-fog">
                        <tr>
                            <th class="p-3 font-bold">Servicio Prestado</th>
                            <th class="p-3 font-bold text-center">Total Citas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cero-fog/60">
                        <?php
                        if (isset($reporte_servicios)) {
                            if (!empty($reporte_servicios)) {
                                foreach ($reporte_servicios as $f) {
                                    echo "<tr class='hover:bg-cero-cream/40'>";
                                    echo "<td class='p-3 font-bold text-cero-black'>" . htmlspecialchars($f['nombre_servicio']) . "</td>";
                                    echo "<td class='p-3 text-center font-mono font-semibold'>" . htmlspecialchars($f['cantidad_citas']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2' class='p-4 text-center text-cero-muted'>No hay servicios registrados en el sistema.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
            </section>

            <section id="precios" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <h2 class="text-2xl font-bold text-cero-black mb-6 flex items-center gap-2">
                        <i data-lucide="dollar-sign" class="w-7 h-7 text-cero-honey"></i>
                        Gestión de Precios de Servicios
                    </h2>

                    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                        <div class="flash-message mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                            <p class="text-green-800 font-bold"><?= htmlspecialchars($_SESSION['success_msg'] ?? 'Precios actualizados correctamente.'); if (isset($_SESSION['success_msg'])) unset($_SESSION['success_msg']); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                        <div class="flash-message mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                            <p class="text-red-800 font-bold"><?= htmlspecialchars($_SESSION['error_msg'] ?? 'Ocurrió un error al guardar los precios. Intenta de nuevo.'); if (isset($_SESSION['error_msg'])) unset($_SESSION['error_msg']); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php $user_role = $_SESSION['rol'] ?? ''; $canEdit = ($user_role === 'Administrador'); ?>
                    <?php if($user_role === 'Administrador' || $user_role === 'Recepcionista'): ?>

                        <form action="/cero_project/public/admin/servicios/actualizar" method="POST" class="space-y-6">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-cero-wheat text-cero-ink">
                                            <th class="p-4 font-bold">Servicio</th>
                                            <th class="p-4 font-bold">Tipo</th>
                                            <th class="p-4 font-bold text-right w-56">Precio (USD)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-cero-fog/60">
                                        <?php
                                            $servicios_a_mostrar = [];
                                            if (!empty($servicios)) {
                                                $servicios_a_mostrar = array_filter($servicios, function ($servicio) {
                                                    $nombre = trim(mb_strtolower($servicio['nombre_servicio']));
                                                    return !in_array($nombre, ['acondicionamiento de élite', 'acondicionamiento de elite'], true);
                                                });
                                            }
                                        ?>
                                        <?php if (!empty($servicios_a_mostrar)): ?>
                                            <?php foreach ($servicios_a_mostrar as $servicio): ?>
                                                <tr class="hover:bg-cero-cream/40 transition-colors">
                                                    <td class="p-4 font-bold text-cero-black"><?= htmlspecialchars($servicio['nombre_servicio']) ?></td>
                                                    <td class="p-4 text-cero-muted"><?= htmlspecialchars($servicio['tipo_servicio']) ?></td>
                                                    <td class="p-4 text-right">
                                                        <div class="relative inline-block w-48">
                                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cero-muted font-bold">$</span>
                                                            <input
                                                                type="number"
                                                                step="0.01"
                                                                min="0"
                                                                max="10000"
                                                                name="precios[<?= (int)$servicio['id_servicio'] ?>]"
                                                                value="<?= number_format((float)$servicio['precio'], 2, '.', '') ?>"
                                                                class="w-full pl-8 pr-3 py-2 border border-cero-fog rounded-lg outline-none focus:ring-2 focus:ring-cero-amber focus:border-cero-amber transition font-mono font-bold text-cero-black"
                                                                <?= $canEdit ? 'required' : 'disabled aria-disabled="true"' ?>
                                                            >
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="p-8 text-center text-cero-muted">No hay servicios registrados en el sistema.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex items-center justify-end pt-6 border-t border-cero-fog">
                                <?php if ($canEdit): ?>
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold px-8 py-3 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                        <i data-lucide="save" class="w-4 h-4"></i>
                                        Guardar Cambios
                                    </button>
                                <?php else: ?>
                                <?php endif; ?>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="p-6 bg-red-50 border border-red-200 rounded-xl text-red-800 font-bold">
                            No tienes permisos para acceder a esta sección.
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="usuarios" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <h2 class="text-2xl font-bold text-cero-black mb-6 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-7 h-7 text-cero-honey"></i>
                        Visualización y Gestión de Personal
                    </h2>
                    
                    <form action="/cero_project/public/admin/crear_empleado" method="POST" class="grid grid-cols-2 gap-4 mb-8 bg-cero-cream/40 p-6 rounded-lg border border-cero-fog">
                        <h3 class="col-span-2 font-bold text-cero-ink mb-2">Registrar Nuevo Empleado</h3>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Nombre del empleado:</label>
                            <input type="text" name="nombre" placeholder="Ej. María González" required class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Rol:</label>
                            <select name="id_rol" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none bg-white focus:ring-2 focus:ring-cero-amber">
                            <option value="2">Especialista (Doctor/Fisio/Nutri)</option>
                            <option value="3">Entrenador</option>
                            <option value="4">Recepcionista</option>
                            <option value="1">Administrador</option>
                        </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Correo corporativo:</label>
                            <input type="email" name="correo" placeholder="correo@clinica.com" required class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Contraseña:</label>
                            <input type="password" name="password" placeholder="Ingrese la contraseña aquí" required class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <button type="submit" class="col-span-2 inline-flex items-center justify-center gap-2 bg-cero-black text-cero-sun font-bold py-3 rounded-xl hover:bg-cero-ink border border-cero-line transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="user-plus" class="w-4 h-4"></i> Crear Acceso</button>
                    </form>
                </div>
            </section>

            <section id="pacientes" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <h2 class="text-2xl font-bold text-cero-black mb-6 flex items-center gap-2">
                        <i data-lucide="folder-open" class="w-7 h-7 text-cero-honey"></i>
                        Directorio Total de Pacientes
                    </h2>
                    
                    <!-- Campo de búsqueda -->
                    <div class="mb-6">
                        <label for="searchPacientes" class="block text-xs font-bold text-cero-ink mb-1">Buscar por cédula o nombre:</label>
                        <input type="text" id="searchPacientes" placeholder="Escribe para filtrar la tabla..." class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="pacientesTable">
                            <thead>
                                <tr class="bg-cero-wheat text-cero-ink">
                                    <th class="p-4 font-bold">ID</th>
                                    <th class="p-4 font-bold">Cédula</th>
                                    <th class="p-4 font-bold">Nombre Completo</th>
                                    <th class="p-4 font-bold">Correo</th>
                                    <th class="p-4 font-bold">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pacientes)): ?>
                                    <?php foreach ($pacientes as $paciente): ?>
                                        <tr class="border-b border-cero-fog hover:bg-cero-cream/40">
                                            <td class="p-4 font-medium"><?= htmlspecialchars($paciente['id_paciente']) ?></td>
                                            <td class="p-4 font-medium"><?= htmlspecialchars($paciente['cedula']) ?></td>
                                            <td class="p-4 font-bold"><?= htmlspecialchars($paciente['nombre_completo']) ?></td>
                                            <td class="p-4 text-sm"><?= htmlspecialchars($paciente['correo']) ?></td>
                                            <td class="p-4 space-x-2">
                                                <a href="/cero_project/public/admin/ver_paciente?id=<?= $paciente['id_paciente'] ?>" class="inline-flex items-center gap-1 bg-cero-amber text-cero-black px-3 py-1.5 rounded-lg text-sm hover:bg-cero-honey font-bold transition-all duration-200 shadow-sm">
                                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Datos</a>
                                                <a href="/cero_project/public/admin/eliminar_paciente?id=<?= $paciente['id_paciente'] ?>" onclick="return confirm('¿Estás seguro de eliminar este paciente?')" class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-red-700 font-bold transition-all duration-200 shadow-sm border border-red-800/20">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Eliminar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-cero-muted">No hay pacientes registrados aún.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="citas" class="tab-content max-w-6xl mx-auto">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
        <h2 class="text-2xl font-bold text-cero-black mb-6 flex items-center gap-2">
            <i data-lucide="calendar-days" class="w-7 h-7 text-cero-honey"></i>
            Administración de Citas
        </h2>

        <form action="/cero_project/public/dashboard" method="GET" class="mb-6 flex flex-col sm:flex-row flex-wrap gap-3 sm:items-end">
            <input type="hidden" name="tab" value="citas">
            <div class="flex-1 min-w-[200px]">
<label for="citas_cedula_search" class="block text-xs font-bold text-cero-ink mb-1.5">buscar por cedula del paciente</label>
<input type="text" id="citas_cedula_search" name="citas_cedula" value="<?= isset($citas_filtro_raw) ? htmlspecialchars($citas_filtro_raw, ENT_QUOTES, 'UTF-8') : '' ?>" placeholder="Ej: 12345678" autocomplete="off" class="w-full px-4 py-2.5 border border-cero-fog rounded-xl outline-none focus:ring-2 focus:ring-cero-amber">
            </div>
            <div class="flex gap-2 shrink-0">
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar
                </button>
                <a href="/cero_project/public/dashboard?tab=citas" class="inline-flex items-center justify-center gap-2 bg-cero-mist hover:bg-cero-fog text-cero-ink font-bold px-4 py-2.5 rounded-xl transition border border-cero-fog text-sm">Limpiar</a>
            </div>
        </form>

        <?php if (!empty($citas_paciente_resumen) && !empty($citas_filtro_raw)): ?>
            <div class="mb-4 rounded-xl border border-cero-sun/40 bg-cero-cream/80 px-4 py-3 text-sm text-cero-ink">
                <span class="font-bold text-cero-black">Resultado:</span>
                <?= htmlspecialchars($citas_paciente_resumen['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>
                <span class="text-cero-muted">— Cédula <?= htmlspecialchars($citas_paciente_resumen['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?> — ID <?= htmlspecialchars((string) $citas_paciente_resumen['id_paciente'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($citas_busqueda_sin_paciente)): ?>
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">
                No se encontró un paciente con esa cédula o ID. Revise el dato e intente de nuevo.
            </div>
        <?php endif; ?>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-cero-black text-cero-sun">
                    <th class="p-4 font-bold">Paciente</th>
                    <th class="p-4 font-bold">Servicio</th>
                    <th class="p-4 font-bold">Fecha</th>
                    <th class="p-4 font-bold">Estado</th>
                    <th class="p-4 font-bold text-center">Acciones</th> </tr>
            </thead>
            <tbody>
                <?php if (!empty($citas_busqueda_sin_paciente)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-cero-muted font-medium">Sin resultados para la búsqueda indicada.</td>
                    </tr>
                <?php elseif (empty($citas)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-cero-muted font-medium"><?= !empty($citas_filtro_raw) ? 'Este paciente no tiene citas registradas.' : 'No hay citas registradas aún.' ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($citas as $cita): ?>
                        <tr class="border-b hover:bg-cero-cream/40">
                            <td class="p-4 font-bold"><?= htmlspecialchars($cita['nombre_completo']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($cita['nombre_servicio'] ?? 'Servicio #' . $cita['id_servicio']) ?></td>
                            <td class="p-4 text-sm"><?= date('d/m/Y H:i', strtotime($cita['fecha_hora'])) ?></td>
                            <td class="p-4">
                                <?php 
                                $estado_class = match($cita['estado_cita']) {
                                    'Pendiente' => 'bg-cero-butter text-cero-bronze',
                                    'Confirmada' => 'bg-cero-sun text-cero-black',
                                    'Completada' => 'bg-green-100 text-green-800',
                                    default => 'bg-cero-mist text-cero-ink'
                                };
                                ?>
                                <span class="<?= $estado_class ?> px-2 py-1 rounded text-xs font-bold"><?= htmlspecialchars($cita['estado_cita']) ?></span>
                            </td>
                            <td class="p-4 flex items-center justify-center gap-2">
                                <?php if ($cita['estado_cita'] === 'Pendiente'): ?>
                                    <form action="/cero_project/public/citas/actualizar_estado" method="POST" class="inline">
                                        <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
                                        <input type="hidden" name="estado" value="Confirmada">
                                        <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded-lg font-bold text-xs hover:bg-cero-gold transition shadow-sm border border-cero-amber/30">Confirmar</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($cita['estado_cita'] === 'Confirmada'): ?>
                                    <form action="/cero_project/public/citas/actualizar_estado" method="POST" class="inline">
                                        <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
                                        <input type="hidden" name="estado" value="Completada">
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-lg font-bold text-xs hover:bg-green-700 transition shadow-sm">Completar</button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($cita['estado_cita'] === 'Completada'): ?>
                                    <!-- Botón de archivar removido para citas completadas -->
                                <?php else: ?>
                                    <form action="/cero_project/public/citas/eliminar" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de rechazar esta solicitud?');">
                                        <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
                                        <input type="hidden" name="accion" value="rechazar">
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg font-bold text-xs hover:bg-red-700 transition shadow-sm">Rechazar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

            <section id="evolucion" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <h2 class="text-2xl font-bold text-cero-black mb-6 flex items-center gap-2">
                        <i data-lucide="stethoscope" class="w-7 h-7 text-cero-honey"></i>
                        Expediente (Fisioterapia / Nutrición)
                    </h2>
                    
                    <!-- Formulario de búsqueda -->
                    <form action="/cero_project/public/dashboard" method="GET" class="mb-6 space-y-2">
                        <input type="hidden" name="tab" value="evolucion">
                        <?php if (isset($_SESSION['paciente_id']) && $paciente_evolucion): ?>
                            <!-- ✅ PACIENTE YA SELECCIONADO - Mostrar información -->
                            <div class="bg-cero-cream border border-cero-sun/40 rounded-md p-4 mb-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-cero-bronze">
                                        Paciente seleccionado: <?= htmlspecialchars($paciente_evolucion['nombre_completo']) ?>
                                    </h3>
                                    <p class="text-sm text-cero-muted">
                                        Cédula: <?= htmlspecialchars($paciente_evolucion['cedula']) ?> | 
                                        ID: <?= $paciente_evolucion['id_paciente'] ?>
                                    </p>
                                </div>
                                <a href="/cero_project/public/clear_paciente" class="text-xs text-cero-amber hover:underline font-bold">
                                    Cambiar paciente
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Mostrar formulario de búsqueda -->
                            <label for="evolucion_id_search" class="block text-xs font-bold text-cero-ink">Buscar por cédula del paciente</label>
                            <div class="flex gap-4 flex-wrap">
                                <input type="text" id="evolucion_id_search" name="evolucion_id" placeholder="Ej: 123456" autocomplete="off" class="flex-1 min-w-[200px] px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="search" class="w-4 h-4"></i> Buscar paciente
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Mostrar datos del paciente si se buscó -->
                    <?php if ($paciente_evolucion): ?>
                        <div class="bg-cero-cream border border-cero-sun/40 rounded-md p-4 mb-6">
                            <h3 class="text-lg font-bold text-cero-bronze mb-2">Datos del Paciente</h3>
                            <p><strong>ID:</strong> <?= htmlspecialchars($paciente_evolucion['id_paciente']) ?></p>
                            <p><strong>Cédula:</strong> <?= htmlspecialchars($paciente_evolucion['cedula']) ?></p>
                            <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($paciente_evolucion['nombre_completo']) ?></p>
                            <p><strong>Fecha de Nacimiento:</strong> <?= date('d/m/Y', strtotime($paciente_evolucion['fecha_nacimiento'])) ?></p>
                            <p><strong>Fecha de Registro:</strong> <?= date('d/m/Y', strtotime($paciente_evolucion['fecha_registro'])) ?></p>
                        </div>

                        <div class="bg-white border border-cero-fog rounded-2xl p-6 shadow-sm mb-8">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Plan Nutricional</h3>
                            <form action="/cero_project/public/nutricion/registrar" method="POST" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente_evolucion['id_paciente']) ?>">
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Calorías totales</label>
                                    <input type="number" step="0.1" name="calorias_totales" placeholder="Ej. 2200" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Proteínas (g)</label>
                                    <input type="number" name="proteinas_g" placeholder="Ej. 130" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Carbohidratos (g)</label>
                                    <input type="number" name="carbohidratos_g" placeholder="Ej. 260" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Grasas (g)</label>
                                    <input type="number" name="grasas_g" placeholder="Ej. 70" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div class="lg:col-span-4">
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-3 px-6 rounded-xl border border-cero-line transition-all duration-200 shadow-sm w-full">
                                        <i data-lucide="save" class="w-4 h-4"></i> Guardar Plan Nutricional</button>
                                </div>
                            </form>
                        </div>

                        <div class="bg-white border border-cero-fog rounded-2xl p-6 shadow-sm mb-8">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Plan de Entrenamiento</h3>
                            <form action="/cero_project/public/entrenamiento/registrar" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente_evolucion['id_paciente']) ?>">
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Coach</label>
                                    <select name="id_usuario" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                        <option value="">Selecciona un coach</option>
                                        <?php foreach ($coaches as $coach): ?>
                                            <option value="<?= htmlspecialchars($coach['id_usuario']) ?>"><?= htmlspecialchars($coach['nombre_completo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Objetivo Deportivo</label>
                                    <input type="text" name="objetivo_deportivo" placeholder="Ej. Fuerza explosiva" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div class="lg:col-span-3">
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Descripción de Rutina</label>
                                    <textarea name="descripcion_rutina" rows="3" placeholder="Detalles del plan de entrenamiento" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-cero-ink mb-1">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-3 px-5 rounded-xl border border-cero-line transition-all duration-200 shadow-sm">
                                        <i data-lucide="save" class="w-4 h-4"></i> Guardar Plan de entrenamiento
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="bg-white border border-cero-fog rounded-2xl p-6 shadow-sm mb-8">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Historial de Planes Nutricionales</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Fecha Registro</th><th class="p-2.5 font-bold text-center">Calorías</th><th class="p-2.5 font-bold">Macros</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php if (!empty($plan_nutricionales)): ?>
                                            <?php foreach ($plan_nutricionales as $plan): ?>
                                                <tr class="hover:bg-cero-cream/40">
                                                    <td class="p-2.5 font-mono text-cero-muted"><?= date('d/m/Y H:i', strtotime($plan['fecha_registro'])) ?></td>
                                                    <td class="p-2.5 text-center font-bold"><?= htmlspecialchars($plan['requerimiento_calorico']) ?> kcal</td>
                                                    <td class="p-2.5"><?= htmlspecialchars($plan['distribucion_macros']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="p-3 text-center text-cero-muted">No hay planes nutricionales registrados para este paciente.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white border border-cero-fog rounded-2xl p-6 shadow-sm mb-8">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Historial de Planes de Entrenamiento</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Fecha Inicio</th><th class="p-2.5 font-bold">Fecha Fin</th><th class="p-2.5 font-bold">Objetivo</th><th class="p-2.5 font-bold">Coach</th><th class="p-2.5 font-bold text-center">Estado</th><th class="p-2.5 font-bold text-center">Acciones</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php if (!empty($planes_entrenamiento)): ?>
                                            <?php foreach ($planes_entrenamiento as $plan): ?>
                                                <tr class="hover:bg-cero-cream/40">
                                                    <td class="p-2.5 font-mono text-cero-muted"><?= date('d/m/Y', strtotime($plan['fecha_inicio'])) ?></td>
                                                    <td class="p-2.5 font-mono"><?= date('d/m/Y', strtotime($plan['fecha_fin'])) ?></td>
                                                    <td class="p-2.5">
                                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                            <span class="text-sm font-medium text-cero-ink break-words">
                                                                <?= htmlspecialchars($plan['objetivo_deportivo']) ?>
                                                            </span>
                                                            <button type="button" class="toggle-plan-detail inline-flex items-center gap-2 rounded-full border border-cero-fog bg-white/95 px-3 py-2 text-xs font-semibold text-cero-ink transition duration-200 hover:bg-cero-cream focus:outline-none focus:ring-2 focus:ring-cero-amber" aria-expanded="false" aria-controls="planDetail-<?= $plan['id_plan'] ?>" data-plan-id="<?= htmlspecialchars($plan['id_plan'], ENT_QUOTES) ?>">
                                                                <span class="sr-only">Alternar detalle de rutina</span>
                                                                <span class="chevron-icon transition-transform duration-200">▾</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="p-2.5 text-cero-muted"><?= htmlspecialchars($plan['coach']) ?></td>
                                                    <td class="p-2.5 text-center font-bold"><?= htmlspecialchars($plan['estado_plan']) ?></td>
                                                    <td class="p-2.5 text-center space-y-2">
                                                        <?php if ($plan['estado_plan'] === 'Activo'): ?>
                                                            <form action="/cero_project/public/entrenamiento/actualizar_estado" method="POST" class="inline">
                                                                <input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan['id_plan']) ?>">
                                                                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente_evolucion['id_paciente'] ?? $_SESSION['paciente_id'] ?? 0) ?>">
                                                                <input type="hidden" name="estado" value="Completado">
                                                                <button type="submit" class="w-full bg-green-600 text-white px-3 py-1 rounded-lg font-bold text-xs hover:bg-green-700 transition shadow-sm" onclick="return confirm('Marcar este plan como completado?');">Completar</button>
                                                            </form>
                                                            <form action="/cero_project/public/entrenamiento/actualizar_estado" method="POST" class="inline">
                                                                <input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan['id_plan']) ?>">
                                                                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente_evolucion['id_paciente'] ?? $_SESSION['paciente_id'] ?? 0) ?>">
                                                                <input type="hidden" name="estado" value="Inactivo">
                                                                <button type="submit" class="w-full bg-cero-mist text-cero-ink px-3 py-1 rounded-lg font-bold text-xs hover:bg-cero-line transition shadow-sm" onclick="return confirm('Inactivar este plan de entrenamiento?');">Inactivar</button>
                                                            </form>
                                                        <?php elseif ($plan['estado_plan'] === 'Inactivo'): ?>
                                                            <form action="/cero_project/public/entrenamiento/actualizar_estado" method="POST" class="inline">
                                                                <input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan['id_plan']) ?>">
                                                                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente_evolucion['id_paciente'] ?? $_SESSION['paciente_id'] ?? 0) ?>">
                                                                <input type="hidden" name="estado" value="Activo">
                                                                <button type="submit" class="w-full bg-blue-600 text-white px-3 py-1 rounded-lg font-bold text-xs hover:bg-blue-700 transition shadow-sm" onclick="return confirm('Reactivar este plan de entrenamiento?');">Activar</button>
                                                            </form>
                                                            <form action="/cero_project/public/entrenamiento/eliminar" method="POST" class="inline">
                                                                <input type="hidden" name="id_plan" value="<?= htmlspecialchars($plan['id_plan']) ?>">
                                                                <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($paciente_evolucion['id_paciente'] ?? $_SESSION['paciente_id'] ?? 0) ?>">
                                                                <button type="submit" class="w-full bg-red-600 text-white px-3 py-1 rounded-lg font-bold text-xs hover:bg-red-700 transition shadow-sm" onclick="return confirm('Eliminar este plan de entrenamiento?');">Eliminar</button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr class="detail-row bg-[#f8fafc] text-cero-ink" data-plan-id="<?= htmlspecialchars($plan['id_plan'], ENT_QUOTES) ?>">
                                                    <td colspan="6" class="p-0 border-0">
                                                        <div id="planDetail-<?= $plan['id_plan'] ?>" class="detail-panel max-h-0 overflow-hidden px-6 py-0 transition-[max-height,opacity,padding] duration-300 ease-out opacity-0">
                                                            <div class="py-4">
                                                                <p class="mb-2 text-sm font-semibold text-cero-black">Descripción de la rutina</p>
                                                                <p class="whitespace-pre-line text-sm leading-6 text-cero-ink">
                                                                    <?= nl2br(htmlspecialchars($plan['descripcion_rutina'])) ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="p-3 text-center text-cero-muted">No hay planes de entrenamiento registrados para este paciente.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php endif; ?>
                    
                    <form action="/cero_project/public/evolucion/registrar" method="POST" class="grid grid-cols-2 gap-4 mb-8">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-cero-ink mb-1">ID paciente:</label>
                            <input type="number" name="id_paciente" placeholder="Número de expediente" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" value="<?= $paciente_evolucion ? htmlspecialchars($paciente_evolucion['id_paciente']) : '' ?>">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Diagnóstico clínico:</label>
                            <input type="text" name="diagnostico" placeholder="Descripción del diagnóstico" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Tratamiento asignado:</label>
                            <input type="text" name="tratamiento" placeholder="Plan o tratamiento" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <button type="submit" class="col-span-2 inline-flex items-center justify-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-3 rounded-xl border border-cero-line transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="save" class="w-4 h-4"></i> Registrar Evolución</button>
                    </form>

                    <!-- Mostrar historial de evoluciones si hay paciente -->
                    <?php if (isset($evoluciones) && !empty($evoluciones)): ?>
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Historial de Evoluciones Médicas</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse border border-cero-fog">
                                    <thead class="bg-cero-wheat">
                                        <tr>
                                            <th class="p-3 border border-cero-fog font-bold">Fecha</th>
                                            <th class="p-3 border border-cero-fog font-bold">Especialista</th>
                                            <th class="p-3 border border-cero-fog font-bold">Diagnóstico</th>
                                            <th class="p-3 border border-cero-fog font-bold">Tratamiento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($evoluciones as $evolucion): ?>
                                            <tr class="hover:bg-cero-cream/40">
                                                <td class="p-3 border border-cero-fog"><?= date('d/m/Y', strtotime($evolucion['fecha_evaluacion'])) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($evolucion['especialista']) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($evolucion['diagnostico']) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($evolucion['tratamiento']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="kpi" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <h2 class="text-2xl font-bold text-cero-black mb-6 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="w-7 h-7 text-cero-honey"></i>
                        Seguimiento de KPIs (Entrenamiento)
                    </h2>
                    
                    <!-- Formulario de búsqueda -->
                    <form action="/cero_project/public/dashboard" method="GET" class="mb-6 space-y-2">
                        <input type="hidden" name="tab" value="kpi">
                        <?php if (isset($_SESSION['paciente_id']) && $paciente_kpi): ?>
                            <!-- ✅ PACIENTE YA SELECCIONADO - Mostrar información -->
                            <div class="bg-cero-cream border border-cero-sun/40 rounded-md p-4 mb-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-cero-bronze">
                                        Paciente seleccionado: <?= htmlspecialchars($paciente_kpi['nombre_completo']) ?>
                                    </h3>
                                    <p class="text-sm text-cero-muted">
                                        Cédula: <?= htmlspecialchars($paciente_kpi['cedula']) ?> | 
                                        ID: <?= $paciente_kpi['id_paciente'] ?>
                                    </p>
                                </div>
                                <a href="/cero_project/public/clear_paciente" class="text-xs text-cero-amber hover:underline font-bold">
                                    Cambiar paciente
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Mostrar formulario de búsqueda -->
                            <label for="kpi_id_search" class="block text-xs font-bold text-cero-ink">Buscar por cédula del paciente</label>
                            <div class="flex gap-4 flex-wrap">
                                <input type="text" id="kpi_id_search" name="kpi_id" placeholder="Ej: 123456" autocomplete="off" class="flex-1 min-w-[200px] px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="search" class="w-4 h-4"></i> Buscar paciente
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Mostrar datos del paciente si se buscó -->
                    <?php if ($paciente_kpi): ?>
                        <div class="bg-cero-cream border border-cero-sun/40 rounded-md p-4 mb-6">
                            <h3 class="text-lg font-bold text-cero-bronze mb-2">Datos del Paciente</h3>
                            <p><strong>ID:</strong> <?= htmlspecialchars($paciente_kpi['id_paciente']) ?></p>
                            <p><strong>Cédula:</strong> <?= htmlspecialchars($paciente_kpi['cedula']) ?></p>
                            <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($paciente_kpi['nombre_completo']) ?></p>
                            <p><strong>Fecha de Nacimiento:</strong> <?= date('d/m/Y', strtotime($paciente_kpi['fecha_nacimiento'])) ?></p>
                            <p><strong>Fecha de Registro:</strong> <?= date('d/m/Y', strtotime($paciente_kpi['fecha_registro'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/cero_project/public/kpi/registrar" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8 items-end">
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">ID paciente:</label>
                            <input type="number" name="id_paciente" placeholder="Número de expediente" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" value="<?= $paciente_kpi ? htmlspecialchars($paciente_kpi['id_paciente']) : '' ?>">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Peso (kg):</label>
                            <input type="number" step="0.1" name="peso_kg" placeholder="Ej. 72.5" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Fuerza máxima (RM):</label>
                            <input type="text" name="fuerza_maxima" placeholder="Ej. 100" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-cero-ink mb-1">Rango de movimiento:</label>
                            <input type="text" name="rango_movimiento" placeholder="Ej. 0-120°" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-cero-amber text-cero-black font-bold py-2.5 px-4 rounded-xl hover:bg-cero-honey transition-all duration-200 shadow-sm hover:shadow-md">
                            <i data-lucide="save" class="w-4 h-4"></i> Guardar KPI</button>
                        </div>
                    </form>

                    <?php if (isset($kpi_historial) && !empty($kpi_historial)): ?>
                        <div class="mt-4">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Historial de Indicadores</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse border border-cero-fog">
                                    <thead class="bg-cero-wheat">
                                        <tr>
                                            <th class="p-3 border border-cero-fog font-bold">Fecha</th>
                                            <th class="p-3 border border-cero-fog font-bold">Peso (kg)</th>
                                            <th class="p-3 border border-cero-fog font-bold">Fuerza Máxima (RM)</th>
                                            <th class="p-3 border border-cero-fog font-bold">Rango de Movimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($kpi_historial as $registro): ?>
                                            <tr class="hover:bg-cero-cream/40">
                                                <td class="p-3 border border-cero-fog"><?= date('d/m/Y', strtotime($registro['fecha_medicion'])) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($registro['peso_kg']) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($registro['fuerza_maxima']) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($registro['rango_movimiento'] ?? '-') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif ($paciente_kpi): ?>
                        <div class="mt-4 p-4 bg-cero-cream border border-cero-sun/40 rounded-md text-cero-muted">
                            No hay registros de KPI para este paciente todavía.
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="recuperacion" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <h2 class="text-2xl font-bold text-cero-black mb-4 flex items-center gap-2">
                        <i data-lucide="waves" class="w-7 h-7 text-cero-honey"></i>
                        Control de Crioterapia y Sauna
                    </h2>
                    
                    <!-- Formulario de búsqueda -->
                    <form action="/cero_project/public/dashboard" method="GET" class="mb-6 space-y-2">
                        <input type="hidden" name="tab" value="recuperacion">
                        <?php if (isset($_SESSION['paciente_id']) && $paciente_recuperacion): ?>
                            <!-- ✅ PACIENTE YA SELECCIONADO - Mostrar información -->
                            <div class="bg-cero-cream border border-cero-sun/40 rounded-md p-4 mb-4 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-cero-bronze">
                                        Paciente seleccionado: <?= htmlspecialchars($paciente_recuperacion['nombre_completo']) ?>
                                    </h3>
                                    <p class="text-sm text-cero-muted">
                                        Cédula: <?= htmlspecialchars($paciente_recuperacion['cedula']) ?> | 
                                        ID: <?= $paciente_recuperacion['id_paciente'] ?>
                                    </p>
                                </div>
                                <a href="/cero_project/public/clear_paciente" class="text-xs text-cero-amber hover:underline font-bold">
                                    Cambiar paciente
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Mostrar formulario de búsqueda -->
                            <label for="recuperacion_id_search" class="block text-xs font-bold text-cero-ink">Buscar por cédula del paciente</label>
                            <div class="flex gap-4 flex-wrap">
                                <input type="text" id="recuperacion_id_search" name="recuperacion_id" placeholder="Ej: 123456" autocomplete="off" class="flex-1 min-w-[200px] px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" required>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i data-lucide="search" class="w-4 h-4"></i> Buscar paciente
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Mostrar datos del paciente si se buscó -->
                    <?php if ($paciente_recuperacion): ?>
                        <div class="bg-cero-cream border border-cero-sun/40 rounded-md p-4 mb-6">
                            <h3 class="text-lg font-bold text-cero-bronze mb-2">Datos del Paciente</h3>
                            <p><strong>ID:</strong> <?= htmlspecialchars($paciente_recuperacion['id_paciente']) ?></p>
                            <p><strong>Cédula:</strong> <?= htmlspecialchars($paciente_recuperacion['cedula']) ?></p>
                            <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($paciente_recuperacion['nombre_completo']) ?></p>
                            <p><strong>Fecha de Nacimiento:</strong> <?= date('d/m/Y', strtotime($paciente_recuperacion['fecha_nacimiento'])) ?></p>
                            <p><strong>Fecha de Registro:</strong> <?= date('d/m/Y', strtotime($paciente_recuperacion['fecha_registro'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form action="/cero_project/public/recuperacion/registrar" method="POST" class="flex flex-col sm:flex-row gap-4 flex-wrap items-end">
                        <div class="w-full sm:w-1/4 min-w-[160px]">
                            <label class="block text-xs font-bold text-cero-ink mb-1">ID paciente / atleta:</label>
                            <input type="number" name="id_paciente" placeholder="Número de expediente" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber" value="<?= $paciente_recuperacion ? htmlspecialchars($paciente_recuperacion['id_paciente']) : '' ?>">
                        </div>
                        <div class="w-full sm:w-1/4 min-w-[160px]">
                            <label class="block text-xs font-bold text-cero-ink mb-1">Tipo de terapia:</label>
                            <select name="tipo_terapia" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none bg-white focus:ring-2 focus:ring-cero-amber">
                            <option value="Crioterapia">Tina de Hielo (Crioterapia)</option>
                            <option value="Sauna">Sauna Infrarrojo</option>
                        </select>
                        </div>
                        <div class="w-full sm:w-1/4 min-w-[160px]">
                            <label class="block text-xs font-bold text-cero-ink mb-1">Duración (minutos):</label>
                            <input type="number" name="duracion_minutos" min="1" max="480" value="15" placeholder="Ej: 20" class="w-full px-4 py-2 border border-cero-fog rounded-md outline-none focus:ring-2 focus:ring-cero-amber">
                        </div>
                        <div class="w-full sm:w-auto min-w-[200px] self-end">
                            <button type="submit" class="w-full sm:min-w-[200px] inline-flex items-center justify-center gap-2 bg-cero-black text-cero-sun font-bold px-6 py-2.5 rounded-xl border border-cero-line hover:bg-cero-ink transition-all duration-200 shadow-sm">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Registrar sesión</button>
                        </div>
                    </form>

                    <?php if (isset($recuperacion_historial) && !empty($recuperacion_historial)): ?>
                        <div class="mt-8">
                            <h3 class="text-xl font-bold text-cero-black mb-4">Historial de Sesiones de Recuperación</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse border border-cero-fog">
                                    <thead class="bg-cero-wheat">
                                        <tr>
                                            <th class="p-3 border border-cero-fog font-bold">Fecha</th>
                                            <th class="p-3 border border-cero-fog font-bold">Tipo de terapia</th>
                                            <th class="p-3 border border-cero-fog font-bold">Duración</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recuperacion_historial as $sesion): ?>
                                            <tr class="hover:bg-cero-cream/40">
                                                <td class="p-3 border border-cero-fog"><?= date('d/m/Y H:i', strtotime($sesion['fecha_sesion'])) ?></td>
                                                <td class="p-3 border border-cero-fog"><?= htmlspecialchars($sesion['tipo_terapia']) ?></td>
                                                <td class="p-3 border border-cero-fog font-mono"><?= (int)$sesion['duracion_minutos'] ?> min</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif ($paciente_recuperacion): ?>
                        <div class="mt-4 p-4 bg-cero-cream border border-cero-sun/40 rounded-md text-cero-muted">
                            No hay sesiones registradas para este paciente todavía.
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section id="reportes" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    
                    <div class="border-b border-cero-line pb-5 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-cero-black flex items-center gap-2">
                                <i data-lucide="line-chart" class="w-7 h-7 text-cero-honey"></i>
                                Módulo de Inteligencia de Negocios (11 Reportes)
                            </h2>
                            <p class="text-xs text-cero-muted mt-1">Consultas transaccionales normalizadas renderizadas en tablas visuales con exportación a PDF individual.</p>
                        </div>
                        
                        <div class="flex gap-1 bg-cero-cream/60 p-1 rounded-xl border border-cero-fog font-bold text-xs">
                            <button type="button" onclick="alternarSubReportes('operacional')" class="px-3 py-1.5 rounded-lg bg-white shadow-sm text-cero-black subrep-btn" id="btn-sub-operacional">Operacionales (4)</button>
                            <button type="button" onclick="alternarSubReportes('supervision')" class="px-3 py-1.5 rounded-lg text-cero-muted hover:text-cero-ink subrep-btn" id="btn-sub-supervision">Supervisión (4)</button>
                            <?php if($_SESSION['rol'] == 'Administrador'): ?>
                                <button type="button" onclick="alternarSubReportes('gerencial')" class="px-3 py-1.5 rounded-lg text-cero-muted hover:text-cero-ink subrep-btn" id="btn-sub-gerencial">Gerenciales (3)</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="panel-operacional" class="subrep-panel active space-y-8">
                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="calendar-check" class="w-4 h-4 text-cero-honey"></i> Agenda Diaria Activa</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-op-agenda', 'Agenda_Diaria_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <div id="rep-op-agenda" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Agenda Diaria — CERO</h3><p class="text-xs text-cero-muted">Fecha: <?= date('d/m/Y') ?></p></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Paciente</th><th class="p-2.5 font-bold">Servicio</th><th class="p-2.5 font-bold">Hora</th><th class="p-2.5 font-bold">Estado</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $sql = "SELECT p.cedula, p.nombre_completo, s.nombre_servicio, c.fecha_hora, c.estado_cita FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN servicios s ON c.id_servicio = s.id_servicio WHERE DATE(c.fecha_hora) = CURDATE() ORDER BY c.fecha_hora ASC";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5'>{$r['nombre_servicio']}</td><td class='p-2.5 font-mono'>" . date('H:i', strtotime($r['fecha_hora'])) . "</td><td class='p-2.5 font-bold text-cero-honey'>{$r['estado_cita']}</td></tr>"; }
                                                } else { echo "<tr><td colspan='5' class='p-3 text-center text-cero-muted'>No hay citas programadas para hoy.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='5' class='p-3 text-center text-cero-rust'>Error al procesar consulta.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="snowflake" class="w-4 h-4 text-cero-honey"></i> Monitoreo Transaccional Crioterapia/Sauna</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-op-crio', 'Crioterapia_Diaria_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="operacional">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep2" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep2" name="fecha_inicio_rep2" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep2'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep2" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep2" name="fecha_fin_rep2" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep2'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep2" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep2" name="cedula_rep2" type="text" value="<?= htmlspecialchars($_GET['cedula_rep2'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-op-crio" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Bitácora de Crioterapia — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Atleta</th><th class="p-2.5 font-bold">Terapia</th><th class="p-2.5 font-bold text-center">Duración</th><th class="p-2.5 font-bold">Registro</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep2 = $_GET['fecha_inicio_rep2'] ?? '';
                                                $fecha_fin_rep2 = $_GET['fecha_fin_rep2'] ?? '';
                                                $cedula_rep2 = trim($_GET['cedula_rep2'] ?? '');
                                                $cedula_rep2 = str_replace("'", "''", $cedula_rep2);
                                                $where_clauses = [];
                                                if (!empty($fecha_inicio_rep2) && !empty($fecha_fin_rep2)) {
                                                    $where_clauses[] = "DATE(r.fecha_sesion) BETWEEN '$fecha_inicio_rep2' AND '$fecha_fin_rep2'";
                                                } elseif (!empty($fecha_inicio_rep2)) {
                                                    $where_clauses[] = "DATE(r.fecha_sesion) >= '$fecha_inicio_rep2'";
                                                } elseif (!empty($fecha_fin_rep2)) {
                                                    $where_clauses[] = "DATE(r.fecha_sesion) <= '$fecha_fin_rep2'";
                                                }
                                                if (!empty($cedula_rep2)) {
                                                    $where_clauses[] = "p.cedula = '$cedula_rep2'";
                                                }
                                                $where_clause = '';
                                                if (!empty($where_clauses)) {
                                                    $where_clause = 'WHERE ' . implode(' AND ', $where_clauses);
                                                }
                                                $sql = "SELECT p.cedula, p.nombre_completo, r.tipo_terapia, r.duracion_minutos, r.fecha_sesion FROM recuperacion_termica r JOIN pacientes p ON r.id_paciente = p.id_paciente $where_clause ORDER BY r.fecha_sesion DESC LIMIT 20";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5'>{$r['tipo_terapia']}</td><td class='p-2.5 text-center font-mono'>{$r['duracion_minutos']} min</td><td class='p-2.5 font-mono'>" . date('H:i', strtotime($r['fecha_sesion'])) . "</td></tr>"; }
                                                } else { echo "<tr><td colspan='5' class='p-3 text-center text-cero-muted'>No se han procesado terapias en el rango seleccionado.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='5' class='p-3 text-center text-cero-rust'>Error de conexión.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="clipboard-pulse" class="w-4 h-4 text-cero-honey"></i> Bitácora Clínica Fisioterapia Reciente</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-op-fisio', 'Bitacora_Fisioterapia_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="operacional">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep3" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep3" name="fecha_inicio_rep3" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep3'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep3" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep3" name="fecha_fin_rep3" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep3'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep3" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep3" name="cedula_rep3" type="text" value="<?= htmlspecialchars($_GET['cedula_rep3'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-op-fisio" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Fisioterapia Clínica — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Fecha</th><th class="p-2.5 font-bold">Paciente</th><th class="p-2.5 font-bold">Especialista</th><th class="p-2.5 font-bold">Diagnóstico</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep3 = $_GET['fecha_inicio_rep3'] ?? '';
                                                $fecha_fin_rep3 = $_GET['fecha_fin_rep3'] ?? '';
                                                $cedula_rep3 = trim($_GET['cedula_rep3'] ?? '');
                                                $cedula_rep3 = str_replace("'", "''", $cedula_rep3);
                                                $where_clause = "";
                                                if (!empty($fecha_inicio_rep3) && !empty($fecha_fin_rep3)) {
                                                    $where_clause = "WHERE DATE(e.fecha_evaluacion) BETWEEN '$fecha_inicio_rep3' AND '$fecha_fin_rep3'";
                                                } elseif (!empty($fecha_inicio_rep3)) {
                                                    $where_clause = "WHERE DATE(e.fecha_evaluacion) >= '$fecha_inicio_rep3'";
                                                } elseif (!empty($fecha_fin_rep3)) {
                                                    $where_clause = "WHERE DATE(e.fecha_evaluacion) <= '$fecha_fin_rep3'";
                                                }
                                                if (!empty($cedula_rep3)) {
                                                    $where_clause .= ($where_clause === '' ? 'WHERE ' : ' AND ') . "p.cedula = '$cedula_rep3'";
                                                }
                                                $sql = "SELECT p.cedula, p.nombre_completo AS atleta, u.nombre_completo AS especialista, e.diagnostico, e.fecha_evaluacion FROM evolucion_medica e JOIN pacientes p ON e.id_paciente = p.id_paciente JOIN usuarios u ON e.id_usuario = u.id_usuario $where_clause ORDER BY e.fecha_evaluacion DESC LIMIT 15";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-mono text-cero-muted'>" . date('d/m/Y', strtotime($r['fecha_evaluacion'])) . "</td><td class='p-2.5 font-bold'>{$r['atleta']}</td><td class='p-2.5'>{$r['especialista']}</td><td class='p-2.5'>{$r['diagnostico']}</td></tr>"; }
                                                } else { echo "<tr><td colspan='5' class='p-3 text-center text-cero-muted'>No hay registros de readaptación en el rango seleccionado.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='5' class='p-3 text-center text-cero-rust'>Error de consulta.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="dumbbell" class="w-4 h-4 text-cero-honey"></i> Control Operativo Rutinas Vigentes</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-op-rutinas', 'Rutinas_Vigentes_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="operacional">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep4" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep4" name="fecha_inicio_rep4" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep4'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep4" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep4" name="fecha_fin_rep4" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep4'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep4" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep4" name="cedula_rep4" type="text" value="<?= htmlspecialchars($_GET['cedula_rep4'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-op-rutinas" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Acondicionamiento Físico — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Atleta</th><th class="p-2.5 font-bold">Objetivo Planificado</th><th class="p-2.5 font-bold">Coach Asignado</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep4 = $_GET['fecha_inicio_rep4'] ?? '';
                                                $fecha_fin_rep4 = $_GET['fecha_fin_rep4'] ?? '';
                                                $cedula_rep4 = trim($_GET['cedula_rep4'] ?? '');
                                                $cedula_rep4 = str_replace("'", "''", $cedula_rep4);
                                                $where_clause = "WHERE pe.estado_plan = 'Activo'";
                                                if (!empty($fecha_inicio_rep4) && !empty($fecha_fin_rep4)) {
                                                    $where_clause .= " AND DATE(pe.fecha_inicio) BETWEEN '$fecha_inicio_rep4' AND '$fecha_fin_rep4'";
                                                } elseif (!empty($fecha_inicio_rep4)) {
                                                    $where_clause .= " AND DATE(pe.fecha_inicio) >= '$fecha_inicio_rep4'";
                                                } elseif (!empty($fecha_fin_rep4)) {
                                                    $where_clause .= " AND DATE(pe.fecha_inicio) <= '$fecha_fin_rep4'";
                                                }
                                                if (!empty($cedula_rep4)) {
                                                    $where_clause .= " AND p.cedula = '$cedula_rep4'";
                                                }
                                                $sql = "SELECT p.cedula, p.nombre_completo, pe.objetivo_deportivo, u.nombre_completo AS coach FROM planes_entrenamiento pe JOIN pacientes p ON pe.id_paciente = p.id_paciente JOIN usuarios u ON pe.id_usuario = u.id_usuario $where_clause";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5'>{$r['objetivo_deportivo']}</td><td class='p-2.5 text-cero-muted'>{$r['coach']}</td></tr>"; }
                                                } else { echo "<tr><td colspan='4' class='p-3 text-center text-cero-muted'>Sin planes asignados activos.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='4' class='p-3 text-center text-cero-muted'>Módulo de rutinas listo para integración.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="panel-supervision" class="subrep-panel hidden space-y-8">
                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="trending-up" class="w-4 h-4 text-cero-honey"></i> Auditoría de Progresión (KPIs)</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-sup-kpi', 'Auditoria_KPIs_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="supervision">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep5" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep5" name="fecha_inicio_rep5" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep5'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep5" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep5" name="fecha_fin_rep5" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep5'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep5" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep5" name="cedula_rep5" type="text" value="<?= htmlspecialchars($_GET['cedula_rep5'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-sup-kpi" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Auditoría Biomecánica KPI — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Atleta</th><th class="p-2.5 font-bold text-center">Fecha</th><th class="p-2.5 font-bold text-center">Peso</th><th class="p-2.5 font-bold text-center">Fuerza Máxima (RM)</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep5 = $_GET['fecha_inicio_rep5'] ?? '';
                                                $fecha_fin_rep5 = $_GET['fecha_fin_rep5'] ?? '';
                                                $cedula_rep5 = trim($_GET['cedula_rep5'] ?? '');
                                                $cedula_rep5 = str_replace("'", "''", $cedula_rep5);
                                                $where_clause = "";
                                                if (!empty($fecha_inicio_rep5) && !empty($fecha_fin_rep5)) {
                                                    $where_clause = "WHERE DATE(k.fecha_medicion) BETWEEN '$fecha_inicio_rep5' AND '$fecha_fin_rep5'";
                                                } elseif (!empty($fecha_inicio_rep5)) {
                                                    $where_clause = "WHERE DATE(k.fecha_medicion) >= '$fecha_inicio_rep5'";
                                                } elseif (!empty($fecha_fin_rep5)) {
                                                    $where_clause = "WHERE DATE(k.fecha_medicion) <= '$fecha_fin_rep5'";
                                                }
                                                if (!empty($cedula_rep5)) {
                                                    $where_clause .= ($where_clause === '' ? 'WHERE ' : ' AND ') . "p.cedula = '$cedula_rep5'";
                                                }
                                                $sql = "SELECT p.cedula, p.nombre_completo, k.fecha_medicion, k.peso_kg, k.fuerza_maxima FROM kpi_progresos k JOIN pacientes p ON k.id_paciente = p.id_paciente $where_clause ORDER BY k.fecha_medicion DESC LIMIT 15";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5 text-center font-mono'>" . date('d/m/Y', strtotime($r['fecha_medicion'])) . "</td><td class='p-2.5 text-center font-mono'>{$r['peso_kg']} kg</td><td class='p-2.5 text-center font-bold'>{$r['fuerza_maxima']}</td></tr>"; }
                                                } else { echo "<tr><td colspan='5' class='p-3 text-center text-cero-muted'>No se registran progresiones en el rango seleccionado.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='5' class='p-3 text-center text-cero-rust'>Error de conexión.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-cero-honey"></i> Supervisión de Citas Completadas Recientes</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-sup-citas', 'Citas_Completadas_Recientes_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="supervision">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep6" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep6" name="fecha_inicio_rep6" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep6'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep6" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep6" name="fecha_fin_rep6" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep6'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep6" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep6" name="cedula_rep6" type="text" value="<?= htmlspecialchars($_GET['cedula_rep6'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-sup-citas" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Citas Completadas Recientes — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Paciente</th><th class="p-2.5 font-bold text-center">Servicio</th><th class="p-2.5 font-bold text-center">Fecha/Hora</th><th class="p-2.5 font-bold text-center">Estado</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep6 = $_GET['fecha_inicio_rep6'] ?? '';
                                                $fecha_fin_rep6 = $_GET['fecha_fin_rep6'] ?? '';
                                                $cedula_rep6 = trim($_GET['cedula_rep6'] ?? '');
                                                $cedula_rep6 = str_replace("'", "''", $cedula_rep6);
                                                $where_clause = "WHERE c.estado_cita = 'Completada'";
                                                if (!empty($fecha_inicio_rep6) && !empty($fecha_fin_rep6)) {
                                                    $where_clause = "WHERE c.estado_cita = 'Completada' AND DATE(c.fecha_hora) BETWEEN '$fecha_inicio_rep6' AND '$fecha_fin_rep6'";
                                                } elseif (!empty($fecha_inicio_rep6)) {
                                                    $where_clause = "WHERE c.estado_cita = 'Completada' AND DATE(c.fecha_hora) >= '$fecha_inicio_rep6'";
                                                } elseif (!empty($fecha_fin_rep6)) {
                                                    $where_clause = "WHERE c.estado_cita = 'Completada' AND DATE(c.fecha_hora) <= '$fecha_fin_rep6'";
                                                }
                                                if (!empty($cedula_rep6)) {
                                                    $where_clause .= " AND p.cedula = '$cedula_rep6'";
                                                }
                                                $sql = "SELECT p.cedula, p.nombre_completo AS paciente, s.nombre_servicio, c.fecha_hora, c.estado_cita FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN servicios s ON c.id_servicio = s.id_servicio $where_clause ORDER BY c.fecha_hora DESC LIMIT 20";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) {
                                                        echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['paciente']}</td><td class='p-2.5 text-center'>{$r['nombre_servicio']}</td><td class='p-2.5 text-center font-mono'>" . date('d/m/Y H:i', strtotime($r['fecha_hora'])) . "</td><td class='p-2.5 text-center font-bold'>{$r['estado_cita']}</td></tr>";
                                                    }
                                                } else { echo "<tr><td colspan='5' class='p-3 text-center text-cero-muted'>No hay citas completadas en el rango seleccionado.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='5' class='p-3 text-center text-cero-rust'>Error de conexión.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="apple" class="w-4 h-4 text-cero-honey"></i> Seguimiento y Pautas Nutricionales</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-sup-nutri', 'Pautas_Nutricionales_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="supervision">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep7" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep7" name="fecha_inicio_rep7" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep7'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep7" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep7" name="fecha_fin_rep7" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep7'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep7" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep7" name="cedula_rep7" type="text" value="<?= htmlspecialchars($_GET['cedula_rep7'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-sup-nutri" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Pautas Nutricionales — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Atleta</th><th class="p-2.5 font-bold text-center">Calorías Asignadas</th><th class="p-2.5 font-bold">Distribución Macros</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep7 = $_GET['fecha_inicio_rep7'] ?? '';
                                                $fecha_fin_rep7 = $_GET['fecha_fin_rep7'] ?? '';
                                                $cedula_rep7 = trim($_GET['cedula_rep7'] ?? '');
                                                $cedula_rep7 = str_replace("'", "''", $cedula_rep7);
                                                $where_clause = "";
                                                if (!empty($fecha_inicio_rep7) && !empty($fecha_fin_rep7)) {
                                                    $where_clause = "WHERE DATE(pn.fecha_registro) BETWEEN '$fecha_inicio_rep7' AND '$fecha_fin_rep7'";
                                                } elseif (!empty($fecha_inicio_rep7)) {
                                                    $where_clause = "WHERE DATE(pn.fecha_registro) >= '$fecha_inicio_rep7'";
                                                } elseif (!empty($fecha_fin_rep7)) {
                                                    $where_clause = "WHERE DATE(pn.fecha_registro) <= '$fecha_fin_rep7'";
                                                }
                                                if (!empty($cedula_rep7)) {
                                                    $where_clause .= ($where_clause === '' ? 'WHERE ' : ' AND ') . "p.cedula = '$cedula_rep7'";
                                                }
                                                if (isset($plan_nutricional_actual) && $plan_nutricional_actual && $where_clause === '') {
                                                    $r = $plan_nutricional_actual;
                                                    echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5 text-center font-mono'>{$r['requerimiento_calorico']} kcal</td><td class='p-2.5'>{$r['distribucion_macros']}</td></tr>";
                                                } else {
                                                    $sql = "SELECT p.cedula, p.nombre_completo, pn.requerimiento_calorico, pn.distribucion_macros FROM planes_nutricionales pn JOIN pacientes p ON pn.id_paciente = p.id_paciente $where_clause ORDER BY pn.fecha_registro DESC LIMIT 15";
                                                    $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                    if (!empty($filas)) {
                                                        foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5 text-center font-mono'>{$r['requerimiento_calorico']} kcal</td><td class='p-2.5'>{$r['distribucion_macros']}</td></tr>"; }
                                                    } else { echo "<tr><td colspan='4' class='p-3 text-center text-cero-muted'>Pautas no generadas.</td></tr>"; }
                                                }
                                            } catch (Exception $e) { echo "<tr><td colspan='4' class='p-3 text-center text-cero-muted'>Nutrición en espera de inserción.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4 text-cero-honey"></i> Auditoría Calidad: Citas Rechazadas</h3>
                                <button type="button" onclick="exportarReporteAPDF('rep-sup-rechazos', 'Auditoria_Citas_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                            </div>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="supervision">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep8" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep8" name="fecha_inicio_rep8" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep8'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep8" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep8" name="fecha_fin_rep8" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep8'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="cedula_rep8" class="block text-xs font-bold text-cero-ink mb-1">Cédula</label>
                                    <input id="cedula_rep8" name="cedula_rep8" type="text" value="<?= htmlspecialchars($_GET['cedula_rep8'] ?? '') ?>" placeholder="Ej: 12345678" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                            </form>
                            <div id="rep-sup-rechazos" class="overflow-x-auto">
                                <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Control Incidencias de Citas — CERO</h3></div>
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Cédula</th><th class="p-2.5 font-bold">Paciente</th><th class="p-2.5 font-bold">Servicio</th><th class="p-2.5 font-bold">Fecha</th><th class="p-2.5 font-bold">Estado Final</th></tr></thead>
                                    <tbody class="divide-y">
                                        <?php
                                        if (isset($db)) {
                                            try {
                                                $fecha_inicio_rep8 = $_GET['fecha_inicio_rep8'] ?? '';
                                                $fecha_fin_rep8 = $_GET['fecha_fin_rep8'] ?? '';
                                                $cedula_rep8 = trim($_GET['cedula_rep8'] ?? '');
                                                $cedula_rep8 = str_replace("'", "''", $cedula_rep8);
                                                $where_clause = "WHERE c.estado_cita IN ('Rechazada', 'Cancelada')";
                                                if (!empty($fecha_inicio_rep8) && !empty($fecha_fin_rep8)) {
                                                    $where_clause = "WHERE c.estado_cita IN ('Rechazada', 'Cancelada') AND DATE(c.fecha_hora) BETWEEN '$fecha_inicio_rep8' AND '$fecha_fin_rep8'";
                                                } elseif (!empty($fecha_inicio_rep8)) {
                                                    $where_clause = "WHERE c.estado_cita IN ('Rechazada', 'Cancelada') AND DATE(c.fecha_hora) >= '$fecha_inicio_rep8'";
                                                } elseif (!empty($fecha_fin_rep8)) {
                                                    $where_clause = "WHERE c.estado_cita IN ('Rechazada', 'Cancelada') AND DATE(c.fecha_hora) <= '$fecha_fin_rep8'";
                                                }
                                                if (!empty($cedula_rep8)) {
                                                    $where_clause .= " AND p.cedula = '$cedula_rep8'";
                                                }
                                                $sql = "SELECT p.cedula, p.nombre_completo, s.nombre_servicio, c.fecha_hora, c.estado_cita FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN servicios s ON c.id_servicio = s.id_servicio $where_clause ORDER BY c.fecha_hora DESC LIMIT 15";
                                                $stmt = $db->query($sql); $filas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
                                                if (!empty($filas)) {
                                                    foreach ($filas as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-mono text-cero-muted'>{$r['cedula']}</td><td class='p-2.5 font-bold'>{$r['nombre_completo']}</td><td class='p-2.5'>{$r['nombre_servicio']}</td><td class='p-2.5 font-mono'>" . date('d/m/Y H:i', strtotime($r['fecha_hora'])) . "</td><td class='p-2.5 font-bold text-red-600'>{$r['estado_cita']}</td></tr>"; }
                                                } else { echo "<tr><td colspan='5' class='p-3 text-center text-cero-muted'>No existen citas descartadas en el rango seleccionado.</td></tr>"; }
                                            } catch (Exception $e) { echo "<tr><td colspan='5' class='p-3 text-center text-cero-rust'>Fallo en lectura de auditoría.</td></tr>"; }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if($_SESSION['rol'] == 'Administrador'): ?>
                        <div id="panel-gerencial" class="subrep-panel hidden space-y-8">
                            <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                                <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                    <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="dollar-sign" class="w-4 h-4 text-cero-honey"></i> Consolidado financiero de ingresos por expedientes</h3>
                                    <button type="button" onclick="exportarReporteAPDF('rep-gen-ing-pdf', 'Finanzas_Consolidadas_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                                </div>
                                <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                    <input type="hidden" name="tab" value="reportes">
                                    <input type="hidden" name="subtab" value="gerencial">
                                    <input type="hidden" name="panel" value="gerencial">
                                    <div class="flex-1">
                                        <label for="fecha_inicio_rep9" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                        <input id="fecha_inicio_rep9" name="fecha_inicio_rep9" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep9'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                    </div>
                                    <div class="flex-1">
                                        <label for="fecha_fin_rep9" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                        <input id="fecha_fin_rep9" name="fecha_fin_rep9" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep9'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                    </div>
                                    <div class="flex-none">
                                        <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                        <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                                    </div>
                                </form>
                                <div id="rep-gen-ing-pdf" class="overflow-x-auto">
                                    <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Finanzas Estratégicas — CERO</h3></div>
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Servicio Prestado</th><th class="p-2.5 font-bold text-center">Volumen Atendido</th><th class="p-2.5 font-bold text-right">Proyección Bruta (USD)</th></tr></thead>
                                        <tbody class="divide-y">
                                            <?php
                                            if (isset($reporte_financiero)) {
                                                $filas = $reporte_financiero;
                                                $total_g = 0;
                                                    if (!empty($filas)) {
                                                        $chart_labels = [];
                                                        $chart_vals = [];
                                                        foreach ($filas as $r) {
                                                            $total_g += $r['ingresos'];
                                                            $chart_labels[] = $r['nombre_servicio'];
                                                            $chart_vals[] = (float)$r['ingresos'];
                                                            echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-bold text-cero-black'>{$r['nombre_servicio']}</td><td class='p-2.5 text-center font-mono'>{$r['volumen']}</td><td class='p-2.5 text-right font-mono font-bold text-green-700'>$" . number_format($r['ingresos'], 2) . "</td></tr>";
                                                        }
                                                        echo "<tr class='bg-cero-paper font-bold border-t'><td class='p-2.5'>Total Consolidado:</td><td></td><td class='p-2.5 text-right font-mono text-green-800'>$" . number_format($total_g, 2) . "</td></tr>";
                                                        // render chart data
                                                        echo "</tbody></table>\n";
                                                        echo "<script>window.rep9_labels = " . json_encode($chart_labels, JSON_UNESCAPED_UNICODE) . "; window.rep9_vals = " . json_encode($chart_vals) . ";</script>";
                                                    } else { echo "<tr><td colspan='3' class='p-3 text-center text-cero-muted'>No hay métricas de capital de trabajo.</td></tr>"; }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                                <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                    <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="activity" class="w-4 h-4 text-cero-honey"></i> Prevalencia Patologías Biomecánicas</h3>
                                    <button type="button" onclick="exportarReporteAPDF('rep-gen-pat-pdf', 'Prevalencia_Lesiones_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                                </div>
                                <div id="rep-gen-pat-pdf" class="overflow-x-auto">
                                    <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Cuadro Biomecánico de Lesiones — CERO</h3></div>
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Diagnóstico Clínico</th><th class="p-2.5 font-bold text-center">Frecuencia Incidencia</th></tr></thead>
                                        <tbody class="divide-y">
                                            <?php
                                            if (isset($reporte_patologias)) {
                                                if (!empty($reporte_patologias)) {
                                                    foreach ($reporte_patologias as $r) { echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-bold text-cero-black'>{$r['diagnostico']}</td><td class='p-2.5 text-center font-mono'>{$r['casos']} casos</td></tr>"; }
                                                } else { echo "<tr><td colspan='2' class='p-3 text-center text-cero-muted'>Patologías en blanco.</td></tr>"; }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="border border-cero-fog rounded-xl p-5 bg-white shadow-sm">
                                <div class="flex justify-between items-center mb-4 border-b border-cero-fog/60 pb-3">
                                    <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2"><i data-lucide="trending-up" class="w-4 h-4 text-cero-honey"></i> Servicios Más Demandados</h3>
                                    <button type="button" onclick="exportarReporteAPDF('rep-gen-srv-pdf', 'Servicios_Demandados_CERO')" class="inline-flex items-center gap-1 bg-cero-sun text-cero-black font-bold px-2.5 py-1 rounded text-xs hover:bg-cero-gold transition"><i data-lucide="file-text" class="w-3.5 h-3.5"></i> Exportar a PDF</button>
                                </div>
                                <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                    <input type="hidden" name="tab" value="reportes">
                                    <input type="hidden" name="subtab" value="gerencial">
                                    <input type="hidden" name="panel" value="gerencial">
                                    <div class="flex-1">
                                        <label for="fecha_inicio_rep11" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                        <input id="fecha_inicio_rep11" name="fecha_inicio_rep11" type="date" value="<?= htmlspecialchars(
                                            $_GET['fecha_inicio_rep11'] ?? '', ENT_QUOTES
                                        ) ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                    </div>
                                    <div class="flex-1">
                                        <label for="fecha_fin_rep11" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                        <input id="fecha_fin_rep11" name="fecha_fin_rep11" type="date" value="<?= htmlspecialchars(
                                            $_GET['fecha_fin_rep11'] ?? '', ENT_QUOTES
                                        ) ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                    </div>
                                    <div class="flex-none">
                                        <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                        <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                                    </div>
                                </form>
                                <div id="rep-gen-srv-pdf" class="overflow-x-auto">
                                    <div class="hidden-print-header hidden pb-3 mb-3 border-b"><h3 class="text-lg font-bold">Servicios Más Demandados — CERO</h3></div>
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Servicio Prestado</th><th class="p-2.5 font-bold text-center">Total Citas</th><th class="p-2.5 font-bold text-center">% Demanda</th></tr></thead>
                                        <tbody class="divide-y">
                                            <?php
                                            if (isset($reporte_servicios)) {
                                                if (!empty($reporte_servicios)) {
                                                    foreach ($reporte_servicios as $r) { 
                                                        $porcentaje = $total_citas_servicios > 0 ? round(($r['cantidad_citas'] / $total_citas_servicios) * 100, 1) : 0;
                                                        echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-bold text-cero-black'>{$r['nombre_servicio']}</td><td class='p-2.5 text-center font-mono font-semibold'>{$r['cantidad_citas']}</td><td class='p-2.5 text-center font-mono text-cero-amber'>{$porcentaje}%</td></tr>"; 
                                                    }
                                                } else { echo "<tr><td colspan='3' class='p-3 text-center text-cero-muted'>Sin citas registradas.</td></tr>"; }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </section>

            <?php if($_SESSION['rol'] == 'Administrador'): ?>
                <section id="inteligencia" class="tab-content max-w-6xl mx-auto">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                        <div class="border-b border-cero-line pb-3 mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <h2 class="text-lg font-bold text-cero-black flex items-center gap-2">
                                    <i data-lucide="bar-chart-2" class="w-7 h-7 text-cero-honey"></i>
                                    Tablero de Inteligencia
                                </h2>
                                <p class="text-xs text-cero-muted mt-1">Visualizaciones dedicadas para supervisar demanda, ingresos, estados de citas y retención.</p>
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                                <!-- Probabilidad de completar cita eliminada por solicitud -->
                                <div class="rounded-lg border border-cero-fog bg-cero-paper p-2">
                                    <div class="text-xs font-bold uppercase tracking-wide text-cero-muted">Probabilidad de rechazo por servicio</div>
                                    <div class="mt-1 text-xl font-bold text-cero-black"><?= isset($probabilidad_rechazo_servicio) && !empty($probabilidad_rechazo_servicio) ? $probabilidad_rechazo_servicio[0]['porcentaje_rechazo'] : 0 ?>%</div>
                                    <div class="mt-1 text-xs text-cero-muted">Servicio con mayor rechazo</div>
                                </div>
                                <div class="rounded-lg border border-cero-fog bg-cero-paper p-2">
                                    <div class="text-xs font-bold uppercase tracking-wide text-cero-muted">Probabilidad de retorno de paciente</div>
                                    <div class="mt-1 text-xl font-bold text-cero-black"><?= isset($probabilidad_retorno_paciente) ? $probabilidad_retorno_paciente : 0 ?>%</div>
                                    <div class="mt-1 text-xs text-cero-muted">Pacientes con más de una cita</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                                <div class="rounded-lg border border-cero-fog bg-white p-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <div>
                                            <h4 class="font-semibold text-sm text-cero-ink">Tendencia de nuevos pacientes</h4>
                                            <p class="text-xs text-cero-muted">Nuevas altas mensuales.</p>
                                        </div>
                                    </div>
                                    <canvas id="chart-tendencia-pacientes" height="160" style="height:160px;max-height:160px;"></canvas>
                                </div>
                                <div class="rounded-lg border border-cero-fog bg-white p-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <div>
                                            <h4 class="font-semibold text-sm text-cero-ink">Distribución de estado de citas</h4>
                                            <p class="text-xs text-cero-muted">Porcentaje de estados en el historial.</p>
                                        </div>
                                    </div>
                                    <canvas id="chart-estado-citas" height="160" style="height:160px;max-height:160px;"></canvas>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                                <div class="rounded-lg border border-cero-fog bg-white p-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <div>
                                            <h4 class="font-semibold text-sm text-cero-ink">Demanda de servicios</h4>
                                            <p class="text-xs text-cero-muted">Número de citas según servicio.</p>
                                        </div>
                                    </div>
                                    <canvas id="chart-demanda-servicios" height="220" style="height:220px;max-height:220px;"></canvas>
                                </div>
                                <div class="rounded-lg border border-cero-fog bg-white p-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <div>
                                            <h4 class="font-semibold text-sm text-cero-ink">Ingresos por servicio</h4>
                                            <p class="text-xs text-cero-muted">Ingresos estimados en servicios completados.</p>
                                        </div>
                                    </div>
                                    <canvas id="chart-ingresos-servicios" height="220" style="height:220px;max-height:220px;"></canvas>
                                </div>
                            </div>

                            <div class="rounded-lg border border-cero-fog bg-white p-2">
                                <div class="flex items-center justify-between mb-1">
                                    <div>
                                        <h4 class="font-semibold text-sm text-cero-ink">Rechazo por servicio</h4>
                                        <p class="text-xs text-cero-muted">Tasa de rechazo calculada por servicio.</p>
                                    </div>
                                </div>
                                <canvas id="chart-rechazo-servicio" height="140" style="height:140px;max-height:140px;"></canvas>
                            </div>
                        </div>
                    </div>
                </section>
                <script>
                    window.inteligenciaDashboard = {
                        demandaLabels: <?= json_encode(array_column($reporte_servicios ?? [], 'nombre_servicio'), JSON_UNESCAPED_UNICODE) ?>,
                        demandaData: <?= json_encode(array_map('intval', array_column($reporte_servicios ?? [], 'cantidad_citas'))) ?>,
                        ingresosLabels: <?= json_encode(array_column($reporte_financiero ?? [], 'nombre_servicio'), JSON_UNESCAPED_UNICODE) ?>,
                        ingresosData: <?= json_encode(array_map('floatval', array_column($reporte_financiero ?? [], 'ingresos'))) ?>,
                        estadoLabels: <?= json_encode(array_column($estado_citas_dist ?? [], 'estado_cita'), JSON_UNESCAPED_UNICODE) ?>,
                        estadoData: <?= json_encode(array_map('intval', array_column($estado_citas_dist ?? [], 'total'))) ?>,
                        tendenciaLabels: <?= json_encode(array_column($pacientes_tendencia ?? [], 'periodo'), JSON_UNESCAPED_UNICODE) ?>,
                        tendenciaData: <?= json_encode(array_map('intval', array_column($pacientes_tendencia ?? [], 'total'))) ?>,
                        rechazoLabels: <?= json_encode(array_column($probabilidad_rechazo_servicio ?? [], 'nombre_servicio'), JSON_UNESCAPED_UNICODE) ?>,
                        rechazoData: <?= json_encode(array_map('floatval', array_column($probabilidad_rechazo_servicio ?? [], 'porcentaje_rechazo'))) ?>,
                    };
                    function renderInteligenciaCharts() {
                        if (typeof Chart === 'undefined' || !window.inteligenciaDashboard) return;
                        const optionsCommon = {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 200 },
                            layout: { padding: 8 },
                            plugins: {
                                legend: { display: false, labels: { boxWidth: 10, font: { size: 12 } } },
                                tooltip: { enabled: true, bodyFont: { size: 14 }, titleFont: { size: 14 }, padding: 10, callbacks: { label: function(context) { return context.dataset.label ? context.dataset.label + ': ' + context.formattedValue : context.formattedValue; } } }
                            },
                            scales: {
                                x: { ticks: { font: { size: 12 } }, grid: { display: false } },
                                y: { ticks: { font: { size: 12 } }, grid: { display: false } }
                            },
                            elements: {
                                bar: { borderRadius: 8, barThickness: 18 },
                                point: { radius: 3 }
                            },
                            interaction: { mode: 'nearest', intersect: true }
                        };
                        const buildBar = (ctx, labels, data, label, color) => new Chart(ctx, {
                            type: 'bar',
                            data: { labels, datasets: [{ label, data, backgroundColor: color, borderRadius: 8 }] },
                            options: optionsCommon
                        });
                        const buildLine = (ctx, labels, data, label, color) => new Chart(ctx, {
                            type: 'line',
                            data: { labels, datasets: [{ label, data, fill: true, backgroundColor: color.replace('1)', '0.15)'), borderColor: color, tension: 0.35, pointRadius: 3, pointBackgroundColor: color }] },
                            options: optionsCommon
                        });
                        const buildDoughnut = (ctx, labels, data, colors) => {
                            const doughnutOptions = Object.assign({}, optionsCommon, { plugins: Object.assign({}, optionsCommon.plugins, { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } }) });
                            return new Chart(ctx, { type: 'doughnut', data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 1 }] }, options: doughnutOptions });
                        };
                        const dashboard = window.inteligenciaDashboard;
                        const demandaCtx = document.getElementById('chart-demanda-servicios');
                        if (demandaCtx && dashboard.demandaLabels.length) buildBar(demandaCtx, dashboard.demandaLabels, dashboard.demandaData, 'Citas', 'rgba(251, 191, 36, 0.85)');
                        const ingresosCtx = document.getElementById('chart-ingresos-servicios');
                        if (ingresosCtx && dashboard.ingresosLabels.length) buildBar(ingresosCtx, dashboard.ingresosLabels, dashboard.ingresosData, 'Ingresos', 'rgba(34, 197, 94, 0.85)');
                        const estadoCtx = document.getElementById('chart-estado-citas');
                        if (estadoCtx && dashboard.estadoLabels.length) buildDoughnut(estadoCtx, dashboard.estadoLabels, dashboard.estadoData, ['#fde047', '#fb923c', '#4ade80', '#60a5fa', '#a855f7']);
                        const tendenciaCtx = document.getElementById('chart-tendencia-pacientes');
                        if (tendenciaCtx && dashboard.tendenciaLabels.length) buildLine(tendenciaCtx, dashboard.tendenciaLabels, dashboard.tendenciaData, 'Nuevos pacientes', 'rgba(59, 130, 246, 1)');
                        const rechazoCtx = document.getElementById('chart-rechazo-servicio');
                        if (rechazoCtx && dashboard.rechazoLabels.length) buildBar(rechazoCtx, dashboard.rechazoLabels, dashboard.rechazoData, 'Porcentaje Rechazo', 'rgba(239, 68, 68, 0.85)');
                    }
                    document.addEventListener('DOMContentLoaded', renderInteligenciaCharts);
                </script>
            <?php endif; ?>

            <section id="reportes-erp" class="tab-content max-w-6xl mx-auto">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    
                    <div class="border-b border-cero-line pb-5 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-cero-black flex items-center gap-2">
                                <i data-lucide="line-chart" class="w-7 h-7 text-cero-honey"></i>
                                Módulo de Inteligencia de Negocios (11 Reportes)
                            </h2>
                            <p class="text-xs text-cero-muted mt-1">Consultas transaccionales normalizadas para sustentar los requerimientos de la evaluación.</p>
                        </div>
                        
                        <div class="flex gap-1 bg-cero-cream/60 p-1 rounded-xl border border-cero-fog font-bold text-xs">
                            <button type="button" onclick="verCategoriaERP('operacional')" class="px-3 py-1.5 rounded-lg bg-white shadow-sm text-cero-black rep-btn-erp" id="btn-erp-operacional">Operacionales (4)</button>
                            <button type="button" onclick="verCategoriaERP('supervision')" class="px-3 py-1.5 rounded-lg text-cero-muted hover:text-cero-ink rep-btn-erp" id="btn-erp-supervision">Supervisión (4)</button>
                            <button type="button" onclick="verCategoriaERP('gerencial')" class="px-3 py-1.5 rounded-lg text-cero-muted hover:text-cero-ink rep-btn-erp" id="btn-erp-gerencial">Gerenciales (3)</button>
                        </div>
                    </div>

                    <div id="erp-operacional" class="bloque-erp space-y-6 active">
                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="calendar-check" class="w-4 h-4 text-cero-sun"></i>
                                Agenda Diaria Activa (Recepción y Flujo)
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Listado inmediato para controlar las atenciones programadas en la fecha actual.</p>
                            <div class="bg-cero-black text-cero-sun p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.cedula, p.nombre_completo, s.nombre_servicio, c.fecha_hora, c.estado_cita FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN servicios s ON c.id_servicio = s.id_servicio WHERE DATE(c.fecha_hora) = CURDATE() ORDER BY c.fecha_hora ASC;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="snowflake" class="w-4 h-4 text-cero-sun"></i>
                                Monitoreo Transaccional de Crioterapia y Sauna
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Control operativo en tiempo real de los atletas en áreas de recuperación térmica.</p>
                            <div class="bg-cero-black text-cero-sun p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo, r.tipo_terapia, r.duracion_minutos, r.fecha_sesion FROM recuperacion_termica r JOIN pacientes p ON r.id_paciente = p.id_paciente WHERE DATE(r.fecha_sesion) = CURDATE() ORDER BY r.fecha_sesion DESC;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="clipboard-plus" class="w-4 h-4 text-cero-sun"></i>
                                Bitácora Clínica de Fisioterapia y Readaptación
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Extracción de los diagnósticos y pautas de rehabilitación emitidas recientemente.</p>
                            <div class="bg-cero-black text-cero-sun p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo AS atleta, u.nombre_completo AS especialista, e.diagnostico, e.tratamiento, e.fecha_evaluacion FROM evolucion_medica e JOIN pacientes p ON e.id_paciente = p.id_paciente JOIN usuarios u ON e.id_usuario = u.id_usuario ORDER BY e.fecha_evaluacion DESC LIMIT 30;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="dumbbell" class="w-4 h-4 text-cero-sun"></i>
                                Control Operativo de Rutinas y Objetivos en Gimnasio
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Planes de entrenamiento físico asignados y vigentes en las instalaciones.</p>
                            <div class="bg-cero-black text-cero-sun p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo, pe.objetivo_deportivo, u.nombre_completo AS coach FROM planes_entrenamiento pe JOIN pacientes p ON pe.id_paciente = p.id_paciente JOIN usuarios u ON pe.id_usuario = u.id_usuario WHERE pe.estado_plan = 'Activo';
                            </div>
                        </div>
                    </div>

                    <div id="erp-supervision" class="bloque-erp space-y-6 hidden">
                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="trending-up" class="w-4 h-4 text-cero-amber"></i>
                                Auditoría de Progresión Físico-Atlética (Métricas KPI)
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Supervisión normalizada de la variación histórica de fuerza máxima y peso.</p>
                            <div class="bg-cero-black text-cero-amber p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo, k.fecha_medicion, k.peso_kg, k.fuerza_maxima, k.rango_movimiento FROM kpi_progresos k JOIN pacientes p ON k.id_paciente = p.id_paciente ORDER BY p.nombre_completo ASC, k.fecha_medicion ASC;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="calendar" class="w-4 h-4 text-cero-amber"></i>
                                Supervisión de Citas Completadas Recientes
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Monitoreo de las últimas citas completadas para auditar el cierre de atenciones.</p>
                            <div class="bg-cero-black text-cero-amber p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo AS paciente, s.nombre_servicio, c.fecha_hora, c.estado_cita, u.nombre_completo AS registrador FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN servicios s ON c.id_servicio = s.id_servicio LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.estado_cita = 'Completada' ORDER BY c.fecha_hora DESC LIMIT 20;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="apple" class="w-4 h-4 text-cero-amber"></i>
                                Control de Pautas y Cumplimiento Nutricional
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Auditoría de los requerimientos calóricos y macros prescritos por el especialista.</p>
                            <div class="bg-cero-black text-cero-amber p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo, pn.requerimiento_calorico, pn.distribucion_macros, pn.fecha_registro FROM planes_nutricionales pn JOIN pacientes p ON pn.id_paciente = p.id_paciente ORDER BY pn.fecha_registro DESC;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-cero-amber"></i>
                                Auditoría de Calidad (Citas Rechazadas o Canceladas)
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Listado de reservas no procesadas para supervisar la atención al cliente del staff.</p>
                            <div class="bg-cero-black text-cero-amber p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT p.nombre_completo, s.nombre_servicio, c.fecha_hora, c.estado_cita FROM citas c JOIN pacientes p ON c.id_paciente = p.id_paciente JOIN servicios s ON c.id_servicio = s.id_servicio WHERE c.estado_cita IN ('Rechazada', 'Cancelada');
                            </div>
                        </div>
                    </div>

                    <div id="erp-gerencial" class="bloque-erp space-y-6 hidden">
                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="dollar-sign" class="w-4 h-4 text-cero-honey"></i>
                                Consolidado financiero de ingresos por expedientes
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Cálculo de ingresos estimados cruzando el volumen de atenciones completadas con los costos.</p>
                            <form method="GET" action="" class="flex gap-2 mb-3 items-end">
                                <input type="hidden" name="tab" value="reportes">
                                <input type="hidden" name="subtab" value="gerencial">
                                <div class="flex-1">
                                    <label for="fecha_inicio_rep9b" class="block text-xs font-bold text-cero-ink mb-1">Fecha inicio</label>
                                    <input id="fecha_inicio_rep9b" name="fecha_inicio_rep9" type="date" value="<?= htmlspecialchars($_GET['fecha_inicio_rep9'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-1">
                                    <label for="fecha_fin_rep9b" class="block text-xs font-bold text-cero-ink mb-1">Fecha fin</label>
                                    <input id="fecha_fin_rep9b" name="fecha_fin_rep9" type="date" value="<?= htmlspecialchars($_GET['fecha_fin_rep9'] ?? '') ?>" class="w-full rounded border border-cero-fog bg-white px-2 py-1 text-xs text-cero-black outline-none focus:border-cero-amber">
                                </div>
                                <div class="flex-none">
                                    <button type="button" onclick="limpiarFiltros(this)" class="bg-cero-wheat text-cero-ink px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Limpiar</button>
                                    <button type="submit" class="bg-cero-sun text-cero-black px-3 py-1 rounded text-xs font-bold hover:bg-cero-gold">Filtrar</button>
                                </div>
                            </form>
                            <div class="overflow-x-auto">
                                <?php
                                if (isset($reporte_financiero)) {
                                        $filas = $reporte_financiero;
                                        if (!empty($filas)) {
                                            echo '<table class="w-full text-left border-collapse text-xs">';
                                            echo '<thead class="bg-cero-wheat text-cero-ink border-b"><tr><th class="p-2.5 font-bold">Servicio Prestado</th><th class="p-2.5 font-bold text-center">Volumen Atendido</th><th class="p-2.5 font-bold text-right">Proyección Bruta (USD)</th></tr></thead><tbody class="divide-y">';
                                            $total = 0; $labels = []; $vals = [];
                                            foreach ($filas as $r) {
                                                $total += $r['ingresos'];
                                                $labels[] = $r['nombre_servicio'];
                                                $vals[] = (float)$r['ingresos'];
                                                echo "<tr class='hover:bg-cero-cream/40'><td class='p-2.5 font-bold text-cero-black'>{$r['nombre_servicio']}</td><td class='p-2.5 text-center font-mono'>{$r['volumen']}</td><td class='p-2.5 text-right font-mono font-bold text-green-700'>$" . number_format($r['ingresos'],2) . "</td></tr>";
                                            }
                                            echo "<tr class='bg-cero-paper font-bold border-t'><td class='p-2.5'>Total Consolidado:</td><td></td><td class='p-2.5 text-right font-mono text-green-800'>$" . number_format($total,2) . "</td></tr>";
                                            echo "</tbody></table>";
                                            echo "<script>window.rep9_labels = " . json_encode($labels, JSON_UNESCAPED_UNICODE) . "; window.rep9_vals = " . json_encode($vals) . ";</script>";
                                        } else {
                                            echo "<div class='p-3 text-center text-cero-muted'>No hay métricas en el rango seleccionado.</div>";
                                        }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="activity" class="w-4 h-4 text-cero-honey"></i>
                                Prevalencia Patologías Biomecánicas
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Mapeo de lesiones recurrentes para justificar la adquisición de nuevo equipamiento de salud.</p>
                            <div class="bg-cero-black text-cero-mist p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT diagnostico, COUNT(*) AS total_casos FROM evolucion_medica WHERE diagnostico != '' GROUP BY diagnostico ORDER BY total_casos DESC LIMIT 10;
                            </div>
                        </div>

                        <div class="border border-cero-fog p-5 rounded-xl bg-cero-paper">
                            <h3 class="font-bold text-sm text-cero-ink flex items-center gap-2 mb-1">
                                <i data-lucide="trending-up" class="w-4 h-4 text-cero-honey"></i>
                                Servicios Más Demandados
                            </h3>
                            <p class="text-xs text-cero-muted mb-3">Consulta los servicios más solicitados para identificar la demanda del centro.</p>
                            <div class="bg-cero-black text-cero-mist p-3 rounded-lg text-xs font-mono overflow-x-auto select-all">
                                SELECT s.nombre_servicio, COUNT(c.id_cita) AS cantidad_citas FROM citas c JOIN servicios s ON c.id_servicio = s.id_servicio GROUP BY s.id_servicio, s.nombre_servicio ORDER BY cantidad_citas DESC;
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <script>
            function verCategoriaERP(cat) {
                document.querySelectorAll('.bloque-erp').forEach(el => { 
                    el.classList.add('hidden'); 
                    el.classList.remove('active'); 
                });
                let bloque = document.getElementById('erp-' + cat);
                if (bloque) { 
                    bloque.classList.remove('hidden'); 
                    bloque.classList.add('active'); 
                }
                document.querySelectorAll('.rep-btn-erp').forEach(btn => {
                    btn.classList.remove('bg-white', 'shadow-sm', 'text-cero-black');
                    btn.classList.add('text-cero-muted');
                });
                let activo = document.getElementById('btn-erp-' + cat);
                if (activo) { 
                    activo.classList.remove('text-cero-muted'); 
                    activo.classList.add('bg-white', 'shadow-sm', 'text-cero-black'); 
                }
            }
            </script>

        </div>
    </main>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ----------------
        // Funciones UI
        // ----------------
        function hideFlashMessages(delay = 5000) {
            try {
                const flashes = document.querySelectorAll('.flash-message');
                if (!flashes || flashes.length === 0) return;
                // Remove after delay
                setTimeout(() => {
                    flashes.forEach(f => {
                        try { f.classList.add('hidden'); } catch(e) { f.style.display = 'none'; }
                    });
                }, delay);
            } catch (err) { console.error('hideFlashMessages error', err); }
        }
        function switchTab(tabId) {
            const pacienteId = "<?= isset($_SESSION['paciente_id']) ? addslashes($_SESSION['paciente_id']) : '' ?>";
            const tabParamMap = { 'evolucion': 'evolucion_id', 'kpi': 'kpi_id', 'recuperacion': 'recuperacion_id' };

            try {
                const urlParams = new URLSearchParams(window.location.search);
                const currentTab = urlParams.get('tab');
                const currentPacienteParam = urlParams.get(tabParamMap[tabId]);

                if (pacienteId && tabParamMap[tabId] && (currentTab !== tabId || currentPacienteParam !== pacienteId)) {
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('tab', tabId);
                    newUrl.searchParams.set(tabParamMap[tabId], pacienteId);
                    window.location.href = newUrl.toString();
                    return;
                }

                document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
                const target = document.getElementById(tabId);
                if (target) target.classList.add('active');

                document.querySelectorAll('.nav-btn').forEach(btn => {
                    btn.classList.remove('bg-cero-sun', 'text-cero-black', 'border-l-4', 'border-cero-amber');
                    btn.classList.add('text-cero-mist', 'hover:bg-cero-elevated/80');
                });

                const activeBtn = document.getElementById('btn-' + tabId);
                if (activeBtn) {
                    activeBtn.classList.remove('text-cero-mist', 'hover:bg-cero-elevated/80');
                    activeBtn.classList.add('bg-cero-sun', 'text-cero-black', 'border-l-4', 'border-cero-amber');
                }

                const url = new URL(window.location);
                url.searchParams.set('tab', tabId);
                if (!tabParamMap[tabId]) {
                    url.searchParams.delete('evolucion_id');
                    url.searchParams.delete('kpi_id');
                    url.searchParams.delete('recuperacion_id');
                }
                window.history.pushState({}, '', url);
                // ensure flash messages are scheduled to hide after navigation
                hideFlashMessages(5000);
            } catch (err) {
                console.error('switchTab error', err);
            }
        }

        // Auto-hide any flash messages on initial load after a delay
        document.addEventListener('DOMContentLoaded', function(){
            try { hideFlashMessages(5000); } catch(e) { /* noop */ }
        });

        (function initTabs(){
            const urlParams = new URLSearchParams(window.location.search);
            let tabParam = urlParams.get('tab') || 'resumen';
            switchTab(tabParam);
            if (tabParam === 'reportes') {
                const subtabParam = urlParams.get('subtab') || 'operacional';
                alternarSubReportes(subtabParam);
            }
        })();

        // Ocultar loader de inicio siempre que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function(){
            const dashboardLoader = document.getElementById('dashboardLoader');
            if (dashboardLoader) dashboardLoader.classList.add('hidden');
        });

        // Buscador de pacientes
        document.addEventListener('DOMContentLoaded', function(){
            const searchPacientes = document.getElementById('searchPacientes');
            if (searchPacientes) {
                searchPacientes.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#pacientesTable tbody tr');
                    rows.forEach(row => {
                        if (!row.cells || row.cells.length < 3) return;
                        const cedula = row.cells[1].textContent.toLowerCase();
                        const nombre = row.cells[2].textContent.toLowerCase();
                        row.style.display = (cedula.includes(query) || nombre.includes(query)) ? '' : 'none';
                    });
                });
            }
        });

        // Toggle de detalle de plan
        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('.toggle-plan-detail');
            if (!trigger) return;

            const planId = trigger.dataset.planId;
            const detailRow = document.querySelector(`.detail-row[data-plan-id="${planId}"]`);
            if (!detailRow) return;

            const isOpen = detailRow.classList.toggle('open');
            trigger.setAttribute('aria-expanded', String(isOpen));
            const chevron = trigger.querySelector('.chevron-icon');
            if (chevron) {
                chevron.classList.toggle('rotate-180', isOpen);
            }
        });

        function alternarSubReportes(categoria) {
            document.querySelectorAll('.subrep-panel').forEach(panel => {
                panel.classList.add('hidden');
                panel.classList.remove('active');
            });
            const panelActivo = document.getElementById('panel-' + categoria);
            if (panelActivo) { panelActivo.classList.remove('hidden'); panelActivo.classList.add('active'); }
            document.querySelectorAll('.subrep-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-cero-black');
                btn.classList.add('text-cero-muted');
            });
            const botonActivo = document.getElementById('btn-sub-' + categoria);
            if (botonActivo) { botonActivo.classList.remove('text-cero-muted'); botonActivo.classList.add('bg-white', 'shadow-sm', 'text-cero-black'); }
            try {
                const url = new URL(window.location);
                url.searchParams.set('subtab', categoria);
                window.history.replaceState({}, '', url);
            } catch (err) {
                console.error('alternarSubReportes error', err);
            }
        }

        function limpiarFiltros(button) {
            const form = button.closest('form');
            if (!form) return;
            form.querySelectorAll('input').forEach(input => {
                if (input.type === 'hidden' || input.type === 'submit' || input.type === 'button') return;
                input.value = '';
            });
            form.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        }

        // ----------------
        // Export PDF Maestro (lógica pura)
        // ----------------
        function exportReportMaster(title, columns, rows, filenameBase = 'reporte', imageData = null) {
            try {
                if (!columns || !Array.isArray(columns) || !rows || !Array.isArray(rows)) {
                    console.error('exportReportMaster: columnas o filas inválidas', { title, columns, rows });
                    return;
                }

                const filename = `${filenameBase.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`;

                const wrapper = document.createElement('div');
                wrapper.style.padding = '18px';
                wrapper.style.backgroundColor = '#ffffff';
                wrapper.style.color = '#0a0a0a';
                wrapper.style.fontFamily = 'Inter, Arial, sans-serif';

                const header = document.createElement('div');
                header.style.display = 'flex';
                header.style.justifyContent = 'space-between';
                header.style.alignItems = 'center';
                header.style.marginBottom = '12px';

                const brand = document.createElement('div');
                brand.innerHTML = `<div style="display:flex;align-items:baseline;gap:8px"><h1 style="font-size:20px;margin:0;font-weight:900;color:#0a0a0a">CE<span style="color:#fde047">RO</span></h1><div style="font-size:12px;color:#6b7280;font-weight:700;letter-spacing:0.06em">ALTO RENDIMIENTO</div></div><div style="font-size:12px;color:#6b7280;margin-top:4px">${title}</div>`;
                const meta = document.createElement('div'); meta.style.textAlign = 'right'; meta.style.fontSize = '11px'; meta.style.color = '#374151'; meta.innerHTML = `<div>Generado: ${new Date().toLocaleString()}</div>`;
                header.appendChild(brand); header.appendChild(meta); wrapper.appendChild(header);

                if (imageData) {
                    const imgWrap = document.createElement('div'); imgWrap.style.textAlign = 'center'; imgWrap.style.marginBottom = '10px';
                    const imgEl = document.createElement('img'); imgEl.src = imageData; imgEl.style.maxWidth = '100%'; imgEl.style.height = 'auto'; imgWrap.appendChild(imgEl);
                    wrapper.appendChild(imgWrap);
                }
                const table = document.createElement('table'); table.style.width = '100%'; table.style.borderCollapse = 'collapse'; table.style.fontSize = '11px';
                const thead = document.createElement('thead'); const thr = document.createElement('tr');
                columns.forEach(col => { const th = document.createElement('th'); th.textContent = col; th.style.padding='8px 10px'; th.style.background='#fde047'; th.style.color='#0a0a0a'; th.style.fontWeight='800'; th.style.border='1px solid #e5e7eb'; th.style.textAlign='left'; thr.appendChild(th); });
                thead.appendChild(thr); table.appendChild(thead);

                const tbody = document.createElement('tbody');
                rows.forEach(row => { const tr = document.createElement('tr'); columns.forEach((col, idx) => { const td = document.createElement('td'); td.style.padding='8px 10px'; td.style.border='1px solid #eaeaea'; td.style.verticalAlign='top'; let value=''; if (Array.isArray(row)) { value = row[idx] != null ? String(row[idx]) : ''; } else if (row && typeof row === 'object') { const key = Object.keys(row).find(k => k.toLowerCase() === col.toString().toLowerCase()) || Object.keys(row)[idx]; value = key ? (row[key] != null ? String(row[key]) : '') : ''; } td.innerHTML = value.replace(/\n/g,'<br/>'); tr.appendChild(td); }); tbody.appendChild(tr); });
                table.appendChild(tbody); wrapper.appendChild(table);

                const footer = document.createElement('div'); footer.style.marginTop='10px'; footer.style.fontSize='10px'; footer.style.color='#6b7280'; footer.innerHTML = `<div>Generado: ${new Date().toLocaleString()}</div>`; wrapper.appendChild(footer);

                const opciones = { margin:12, filename: filename, image:{type:'jpeg',quality:0.98}, html2canvas:{scale:2,useCORS:true}, jsPDF:{unit:'mm',format:'a4',orientation:'portrait'} };

                if (typeof html2pdf === 'undefined') { console.error('html2pdf no está cargado en la página.'); return; }

                html2pdf().set(opciones).from(wrapper).toPdf().get('pdf').then(function(pdf){
                    try {
                        const totalPages = pdf.internal.getNumberOfPages();
                        const pageWidth = pdf.internal.pageSize.getWidth();
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        pdf.setFontSize(9); pdf.setTextColor(80);
                        for (let i=1;i<=totalPages;i++){ pdf.setPage(i); pdf.text(`Página ${i} de ${totalPages}`, pageWidth-40, pageHeight-10); pdf.text(`Generado: ${new Date().toLocaleString()}`, 12, pageHeight-10); }
                    } catch(e) { console.warn('No se pudo añadir numeración de páginas al PDF:', e); }
                    try { pdf.save(filename); } catch(e) { console.error('Error al guardar PDF:', e); }
                }).catch(err => console.error('html2pdf fallo:', err));

            } catch (err) { console.error('exportReportMaster fallo:', err); }
        }

        function exportReportFromTable(containerId, title, filenameBase) {
            try {
                const cont = document.getElementById(containerId); if (!cont) { console.error('Contenedor no encontrado', containerId); return; }
                const table = cont.querySelector('table'); if (!table) { console.error('Tabla no encontrada en contenedor', containerId); return; }
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
                const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr => Array.from(tr.querySelectorAll('td')).map(td => td.textContent.trim()));
                // buscar canvas dentro del contenedor y convertir a imagen para incluir en el PDF
                let imageData = null;
                const canvas = cont.querySelector('canvas');
                if (canvas && typeof canvas.toDataURL === 'function') {
                    try { imageData = canvas.toDataURL('image/png'); } catch (e) { console.warn('No se pudo extraer imagen del canvas:', e); }
                }
                exportReportMaster(title, headers, rows, filenameBase, imageData);
            } catch(err) { console.error('exportReportFromTable fallo:', err); }
        }

        function exportarReporteAPDF(idContenedor, nombreBase) {
            const title = nombreBase ? nombreBase.replace(/_/g,' ') : 'Reporte CERO';
            exportReportFromTable(idContenedor, title, nombreBase || 'reporte');
        }

        // Helpers para mostrar/ocultar y renderizar gráficos del reporte 9

        // Asegurar que los formularios en el panel gerencial mantengan pestaña/subtab al enviar
        function ensureGerencialFormTabs() {
            try {
                const panel = document.getElementById('panel-gerencial');
                if (!panel) return;
                const forms = panel.querySelectorAll('form');
                forms.forEach(f => {
                    f.addEventListener('submit', function(e) {
                        try {
                            // garantizar inputs hidden existen y tienen el valor correcto
                            let inpTab = f.querySelector('input[name="tab"]');
                            if (!inpTab) { inpTab = document.createElement('input'); inpTab.type = 'hidden'; inpTab.name = 'tab'; f.prepend(inpTab); }
                            inpTab.value = 'reportes';
                            let inpSub = f.querySelector('input[name="subtab"]');
                            if (!inpSub) { inpSub = document.createElement('input'); inpSub.type = 'hidden'; inpSub.name = 'subtab'; f.prepend(inpSub); }
                            inpSub.value = 'gerencial';
                        } catch(err) { console.error('ensureGerencialFormTabs submit handler error', err); }
                    });
                });
            } catch(e) { console.error('ensureGerencialFormTabs error', e); }
        }
        document.addEventListener('DOMContentLoaded', ensureGerencialFormTabs);
    </script>

    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>

</body>
</html>
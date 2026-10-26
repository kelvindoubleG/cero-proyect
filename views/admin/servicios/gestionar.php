    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Precios - CERO</title>
    <?php require __DIR__ . '/../../partials/tailwind_cero.php'; ?>
</head>
<body class="bg-cero-paper flex h-screen overflow-hidden text-cero-ink font-sans">
    <aside class="w-72 bg-gradient-to-b from-cero-black via-cero-black to-cero-ink text-cero-mist flex flex-col shadow-xl z-20 border-r border-cero-line">
        <div class="p-6 text-center border-b border-cero-line/80 bg-cero-ink/50 backdrop-blur-sm">
            <div class="flex items-center justify-center gap-2 mb-1">
                <i data-lucide="activity" class="w-6 h-6 text-cero-sun"></i>
                <h1 class="text-3xl font-extrabold tracking-widest text-white">CE<span class="text-cero-sun">RO</span></h1>
            </div>
            <p class="text-xs font-bold text-cero-soft mt-1 uppercase tracking-wider">Administración</p>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="/cero_project/public/dashboard" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold text-cero-mist hover:bg-cero-elevated/80 transition-all duration-200 nav-btn">
                <i data-lucide="arrow-left" class="w-5 h-5 shrink-0"></i>
                <span>Volver al Dashboard</span>
            </a>
            
            <div class="pt-4 pb-2">
                <p class="text-xs font-bold text-cero-muted uppercase tracking-wider px-4">Gestión</p>
            </div>
            
            <a href="/cero_project/public/admin/servicios" class="w-full flex items-center gap-3 text-left px-4 py-3 rounded-xl font-bold bg-cero-sun text-cero-black shadow-md nav-btn">
                <i data-lucide="dollar-sign" class="w-5 h-5 shrink-0"></i>
                <span>Precios de Servicios</span>
            </a>
            
        </nav>

            <?php $user_role = $_SESSION['rol'] ?? 'Invitado'; $canEdit = ($user_role === 'Administrador'); ?>
            <div class="p-4 bg-cero-elevated/90 border-t border-cero-line backdrop-blur-sm">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="shield-check" class="w-4 h-4 text-cero-sun shrink-0"></i>
                <p class="text-xs text-cero-soft font-bold">Rol: <?= htmlspecialchars($user_role) ?></p>
            </div>
            <p class="font-bold text-sm truncate text-white pl-6"><?= htmlspecialchars($_SESSION['nombre']) ?></p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen relative">
        <header class="bg-white/90 backdrop-blur-md h-20 px-8 flex items-center justify-between shadow-sm z-10 border-b border-cero-fog">
            <div class="flex items-center gap-3">
                <span class="hidden sm:flex h-10 w-1 rounded-full bg-gradient-to-b from-cero-amber to-cero-sun shadow-sm"></span>
                <h2 class="text-xl font-bold text-cero-ink tracking-tight">Gestión de Precios de Servicios</h2>
            </div>
            <a href="/cero_project/public/logout" class="inline-flex items-center gap-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm border border-red-800/30">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Cerrar sesión</span>
            </a>
        </header>

        <div class="flex-1 overflow-y-auto p-8 bg-cero-cream/40">
            <div class="max-w-4xl mx-auto">
                <!-- Mensajes de éxito/error -->
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="flash-message mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 animate-cero-fade-up">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        <p class="text-green-800 font-bold"><?= htmlspecialchars($_SESSION['success_msg'] ?? 'Precios actualizados correctamente.'); if (isset($_SESSION['success_msg'])) unset($_SESSION['success_msg']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                    <div class="flash-message mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3 animate-cero-fade-up">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                        <p class="text-red-800 font-bold"><?= htmlspecialchars($_SESSION['error_msg'] ?? 'Ocurrió un error al guardar los precios. Intenta de nuevo.'); if (isset($_SESSION['error_msg'])) unset($_SESSION['error_msg']); ?></p>
                    </div>
                <?php endif; ?>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30">
                            <i data-lucide="settings" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-cero-black">Configurar Precios</h2>
                            <p class="text-sm text-cero-muted">Establece los precios en USD para cada servicio del centro</p>
                        </div>
                    </div>

                    <form action="/cero_project/public/admin/servicios/actualizar" method="POST" class="space-y-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-cero-wheat text-cero-ink">
                                        <th class="p-4 font-bold">Servicio</th>
                                        <th class="p-4 font-bold">Tipo</th>
                                        <th class="p-4 font-bold text-right w-48">Precio (USD)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-cero-fog/60">
                                    <?php if (!empty($servicios)): ?>
                                        <?php
                                            $servicios_a_mostrar = array_filter($servicios, function ($servicio) {
                                                $nombre = trim(mb_strtolower($servicio['nombre_servicio']));
                                                return !in_array($nombre, ['acondicionamiento de élite', 'acondicionamiento de elite'], true);
                                            });
                                        ?>
                                        <?php if (!empty($servicios_a_mostrar)): ?>
                                            <?php foreach ($servicios_a_mostrar as $servicio): ?>
                                                <tr class="hover:bg-cero-cream/40 transition-colors">
                                                    <td class="p-4 font-bold text-cero-black">
                                                        <?= htmlspecialchars($servicio['nombre_servicio']) ?>
                                                    </td>
                                                    <td class="p-4 text-cero-muted">
                                                        <?= htmlspecialchars($servicio['tipo_servicio']) ?>
                                                    </td>
                                                    <td class="p-4 text-right">
                                                        <div class="relative inline-block w-40">
                                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-cero-muted font-bold">$</span>
                                                            <input 
                                                                type="number" 
                                                                step="0.01" 
                                                                min="0" 
                                                                max="10000"
                                                                name="precios[<?= $servicio['id_servicio'] ?>]" 
                                                                value="<?= number_format($servicio['precio'], 2, '.', '') ?>"
                                                                class="w-full pl-8 pr-3 py-2 border border-cero-fog rounded-lg outline-none focus:ring-2 focus:ring-cero-amber focus:border-cero-amber transition font-mono font-bold text-cero-black"
                                                                <?= $canEdit ? 'required' : 'disabled aria-disabled="true"' ?>
                                                            >
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="p-8 text-center text-cero-muted">
                                                    No hay servicios registrados en el sistema.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="p-8 text-center text-cero-muted">
                                                No hay servicios registrados en el sistema.
                                            </td>
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
                </div>

            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../../partials/lucide_footer.php'; ?>
    <script>
        function hideFlashMessagesAdmin(delay = 5000) {
            try {
                const flashes = document.querySelectorAll('.flash-message');
                if (!flashes || flashes.length === 0) return;
                setTimeout(() => { flashes.forEach(f => f.classList.add('hidden')); }, delay);
                document.querySelectorAll('.nav-btn').forEach(btn => btn.addEventListener('click', () => flashes.forEach(f => f.classList.add('hidden'))));
            } catch (e) { console.error(e); }
        }
        document.addEventListener('DOMContentLoaded', () => hideFlashMessagesAdmin(5000));
    </script>
</body>
</html>
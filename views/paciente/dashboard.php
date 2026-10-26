<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CERO | Mi Portal Médico</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
</head>
<body class="bg-cero-paper text-cero-ink font-sans relative min-h-screen">
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-cero-sun/10 blur-3xl"></div>
    </div>

    <header class="bg-cero-black/95 backdrop-blur-md text-cero-mist px-6 py-4 flex justify-between items-center shadow-lg border-b border-cero-line">
        <div class="flex items-center gap-2 text-2xl font-black text-white">
            <i data-lucide="layout-template" class="w-7 h-7 text-cero-sun hidden sm:block"></i>
            <div class="leading-tight">
                <span>CE<span class="text-cero-sun">RO</span></span>
                <span class="block text-xs sm:text-sm font-normal text-cero-soft sm:inline sm:ml-2">Portal de Paciente</span>
            </div>
        </div>
        <div class="flex items-center gap-4 sm:gap-6">
            <a href="/cero_project/public/" class="inline-flex items-center gap-2 text-cero-soft hover:text-cero-sun text-sm font-bold transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Web principal</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="/cero_project/public/logout" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 border border-red-800/25 shadow-sm hover:shadow-md">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Salir</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto py-12 px-6 animate-cero-fade-up">
        <h1 class="text-3xl font-extrabold mb-8 text-cero-black tracking-tight flex flex-wrap items-center gap-3">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/25">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </span>
            Bienvenido, <?= explode(' ', $_SESSION['nombre'])[0] ?>
        </h1>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-cero-black">
                    <i data-lucide="calendar-plus" class="w-6 h-6 text-cero-honey"></i>
                    Agendar Nueva Cita
                </h2>
                <form action="/cero_project/public/citas/agendar" method="POST" class="space-y-4">
                    <input type="hidden" name="id_paciente" value="<?= $id_paciente ?>">
                    
                    <div>
                        <label class="block text-sm font-bold text-cero-ink mb-2">Programa:</label>
                        <select name="id_servicio" class="w-full px-4 py-3 rounded-xl border border-cero-fog bg-white outline-none focus:border-cero-amber focus:ring-2 focus:ring-cero-sun/30 transition hover:border-cero-amber/40" aria-label="Programa">
                            <option value="1">Fisioterapia y Readaptación</option>
                            <option value="3">Nutrición</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-cero-ink mb-2">Fecha y hora de preferencia:</label>
                        <input type="datetime-local" name="fecha_hora" required class="w-full px-4 py-3 rounded-xl border border-cero-fog outline-none focus:border-cero-amber focus:ring-2 focus:ring-cero-sun/30 transition hover:border-cero-amber/40">
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold py-3 rounded-xl shadow-md transition-all duration-200 mt-4 hover:-translate-y-0.5 hover:shadow-lg">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Solicitar Cita
                    </button>
                </form>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-cero-fog transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-cero-black">
                    <i data-lucide="users" class="w-6 h-6 text-cero-honey"></i>
                    Especialistas Disponibles
                </h2>
                <div class="space-y-3">
                    <?php foreach ($especialistas as $esp): ?>
                        <div class="flex items-center gap-4 p-4 bg-cero-cream/50 rounded-xl border border-cero-fog transition-all duration-200 hover:border-cero-amber/30 hover:bg-cero-cream">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-cero-black text-cero-sun shrink-0">
                                <i data-lucide="stethoscope" class="w-5 h-5"></i>
                            </span>
                            <div>
                                <p class="font-bold text-cero-black"><?= htmlspecialchars($esp['nombre_completo']) ?></p>
                                <p class="text-sm text-cero-honey font-semibold"><?= htmlspecialchars($esp['nombre_rol']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

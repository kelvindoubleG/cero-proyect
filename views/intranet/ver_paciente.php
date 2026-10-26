<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CERO | Datos del Paciente</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
</head>
<body class="bg-cero-paper flex items-center justify-center min-h-screen p-6 font-sans relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute top-1/4 left-1/4 h-56 w-56 rounded-full bg-cero-sun/12 blur-3xl"></div>
    </div>

    <div class="bg-white/95 backdrop-blur-md p-8 rounded-3xl shadow-cero-glow w-full max-w-2xl border border-cero-fog animate-cero-scale-in">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30 mb-4 mx-auto">
                <i data-lucide="user-round" class="w-7 h-7"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-cero-black tracking-tight">Datos del Paciente</h1>
            <p class="text-sm text-cero-muted mt-2">Información completa del expediente médico.</p>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-2">
                        <i data-lucide="credit-card" class="w-4 h-4 text-cero-honey"></i> Cédula:
                    </label>
                    <p class="w-full px-4 py-3 rounded-xl border border-cero-fog bg-cero-cream/40 text-cero-black transition hover:border-cero-amber/30"><?= htmlspecialchars($paciente_data['cedula']) ?></p>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-2">
                        <i data-lucide="user" class="w-4 h-4 text-cero-honey"></i> Nombre completo:
                    </label>
                    <p class="w-full px-4 py-3 rounded-xl border border-cero-fog bg-cero-cream/40 text-cero-black transition hover:border-cero-amber/30"><?= htmlspecialchars($paciente_data['nombre_completo']) ?></p>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-2">
                        <i data-lucide="cake" class="w-4 h-4 text-cero-honey"></i> Fecha de nacimiento:
                    </label>
                    <p class="w-full px-4 py-3 rounded-xl border border-cero-fog bg-cero-cream/40 text-cero-black transition hover:border-cero-amber/30"><?= htmlspecialchars($paciente_data['fecha_nacimiento']) ?></p>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-2">
                        <i data-lucide="phone" class="w-4 h-4 text-cero-honey"></i> Teléfono:
                    </label>
                    <p class="w-full px-4 py-3 rounded-xl border border-cero-fog bg-cero-cream/40 text-cero-black transition hover:border-cero-amber/30"><?= htmlspecialchars($paciente_data['numero'] ?? 'No registrado') ?> (<?= htmlspecialchars($paciente_data['tipo'] ?? 'N/A') ?>)</p>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-2">
                        <i data-lucide="mail" class="w-4 h-4 text-cero-honey"></i> Correo electrónico:
                    </label>
                    <p class="w-full px-4 py-3 rounded-xl border border-cero-fog bg-cero-cream/40 text-cero-black transition hover:border-cero-amber/30"><?= htmlspecialchars($paciente_data['correo']) ?></p>
                </div>
            </div>
            
            <div class="flex justify-center mt-8">
                <a href="/cero_project/public/dashboard" class="inline-flex items-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-3 px-8 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg border border-cero-line hover:-translate-y-0.5">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CERO | Configuración Inicial</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
    <link rel="stylesheet" href="/cero_project/public/css/style.css">
</head>
<body class="bg-cero-paper min-h-screen flex items-center justify-center p-6 font-sans relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute top-1/3 right-0 h-72 w-72 rounded-full bg-cero-sun/14 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-cero-black/[0.04] blur-2xl"></div>
    </div>

    <div class="w-full max-w-md animate-cero-fade-up">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30 mb-4 mx-auto shadow-sm">
                <i data-lucide="settings" class="w-8 h-8"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-cero-black tracking-tight">CE<span class="text-cero-amber">RO</span></h1>
            <p class="text-sm font-bold text-cero-muted uppercase tracking-wider mt-2">Instalación del sistema</p>
        </div>

        <div class="bg-white/95 backdrop-blur-md rounded-2xl border border-cero-fog shadow-cero-glow p-8 transition-all duration-300 hover:shadow-cero">
            <p class="text-center text-sm text-cero-muted mb-6 leading-relaxed">
                Registre al Administrador principal.
            </p>

            <?php if(!empty($error)): ?>
                <div class="bg-cero-cream border border-cero-honey/50 text-cero-bronze text-center font-bold text-sm mb-4 p-3 rounded-xl flex items-center justify-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="/cero_project/public/setup" class="space-y-4">
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-1">
                        <i data-lucide="user" class="w-4 h-4 text-cero-honey"></i> Nombre completo:
                    </label>
                    <input type="text" name="nombre" required placeholder="Dr. Diego Espinoza" class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-1">
                        <i data-lucide="mail" class="w-4 h-4 text-cero-honey"></i> Usuario de acceso (correo):
                    </label>
                    <input type="email" name="correo" required placeholder="admin@cero.com" class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-bold text-cero-ink mb-1">
                        <i data-lucide="key" class="w-4 h-4 text-cero-honey"></i> Contraseña maestra:
                    </label>
                    <input type="password" name="password" required placeholder="Mínimo 8 caracteres" class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
                <button type="submit" class="w-full mt-3 inline-flex items-center justify-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-3.5 rounded-xl transition-all duration-200 border border-cero-line shadow-md hover:shadow-lg hover:-translate-y-0.5">
                    <i data-lucide="rocket" class="w-4 h-4"></i>
                    Inicializar Sistema
                </button>
            </form>
        </div>
    </div>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

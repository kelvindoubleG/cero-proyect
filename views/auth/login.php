<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CERO | Iniciar Sesión</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
    <style>
        /* Login: visible a 100% zoom, pantallas bajas y safe-area (notch) */
        #cero-login-page {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        #cero-login-card {
            width: 100%;
            max-width: 28rem;
            flex-shrink: 0;
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
            overflow-y: auto;
            overscroll-behavior: contain;
        }
    </style>
</head>
<body id="cero-login-page" class="bg-cero-paper font-sans text-cero-ink relative">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-24 right-0 h-72 w-72 rounded-full bg-cero-sun/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-cero-amber/10 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[min(120vw,56rem)] w-[min(120vw,56rem)] max-w-[90vw] rounded-full border border-cero-sun/[0.06]"></div>
    </div>

    <div id="cero-login-card" class="bg-white/95 backdrop-blur-md p-5 sm:p-7 md:p-9 rounded-2xl sm:rounded-3xl shadow-cero-glow border border-cero-fog animate-cero-scale-in mx-auto w-[min(100%,calc(100vw-2rem))]">
        <div class="text-center mb-5 sm:mb-7 md:mb-8">
            <div class="inline-flex items-center justify-center gap-2 mb-2 sm:mb-3">
                <i data-lucide="shield" class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 text-cero-honey shrink-0"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-cero-black tracking-tight leading-tight">CE<span class="text-cero-amber">RO</span></h1>
            <p class="text-cero-muted mt-2 sm:mt-3 text-sm sm:text-base font-medium">Gestión de Alto Rendimiento</p>
        </div>

        <?php if(!empty($info)): ?>
            <div class="bg-cero-sun/10 border-l-4 border-cero-sun text-cero-black p-3 sm:p-4 mb-4 sm:mb-5 rounded-r-lg shadow-sm flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <p class="text-xs sm:text-sm font-bold leading-snug"><?= htmlspecialchars($info, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>
        <?php if(!empty($error)): ?>
            <div class="bg-cero-cream border-l-4 border-cero-honey text-cero-bronze p-3 sm:p-4 mb-4 sm:mb-5 rounded-r-lg shadow-sm flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <p class="text-xs sm:text-sm font-bold leading-snug"><?= $error ?></p>
            </div>
        <?php endif; ?>

        <form action="/cero_project/public/login" method="POST" class="space-y-4 sm:space-y-5 md:space-y-6">
            <div>
                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-cero-ink mb-1.5 sm:mb-2">
                    <i data-lucide="mail" class="w-4 h-4 text-cero-honey shrink-0"></i> Correo electrónico:
                </label>
                <input type="email" name="correo" required class="w-full px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base rounded-xl border border-cero-fog focus:ring-4 focus:ring-cero-sun/35 focus:border-cero-amber outline-none transition-all duration-200 hover:border-cero-amber/50" placeholder="usuario@ejemplo.com" autocomplete="username">
            </div>
            <div>
                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-cero-ink mb-1.5 sm:mb-2">
                    <i data-lucide="lock" class="w-4 h-4 text-cero-honey shrink-0"></i> Contraseña:
                </label>
                <input type="password" name="password" required class="w-full px-4 sm:px-5 py-3 sm:py-3.5 md:py-4 text-base rounded-xl border border-cero-fog focus:ring-4 focus:ring-cero-sun/35 focus:border-cero-amber outline-none transition-all duration-200 hover:border-cero-amber/50" placeholder="••••••••" autocomplete="current-password">
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-3 sm:py-3.5 md:py-4 text-sm sm:text-base rounded-xl transition-all duration-200 shadow-lg hover:shadow-cero border border-cero-line min-h-[2.75rem]">
                <i data-lucide="log-in" class="w-5 h-5 shrink-0"></i>
                Entrar al Sistema
            </button>
        </form>

        <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 md:pt-7 border-t border-cero-fog text-center">
            <p class="text-cero-muted text-sm sm:text-base leading-relaxed">¿No tienes cuenta?
                <a href="/cero_project/public/registro" class="text-cero-honey font-bold hover:text-cero-amber hover:underline inline-flex items-center gap-1 flex-wrap justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i> Regístrate como Paciente
                </a>
            </p>
            <a href="/cero_project/public/" class="inline-flex items-center justify-center gap-2 mt-3 sm:mt-4 text-xs sm:text-sm text-cero-soft hover:text-cero-ink transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 shrink-0"></i> Volver al inicio
            </a>
        </div>
    </div>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

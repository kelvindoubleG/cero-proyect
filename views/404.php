<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - CERO</title>
    <?php require __DIR__ . '/partials/tailwind_cero.php'; ?>
</head>
<body class="bg-cero-paper text-cero-ink min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-sm border border-cero-fog p-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-12 w-12 rounded-xl bg-cero-cream flex items-center justify-center border border-cero-fog">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-cero-honey"></i>
            </div>
            <h1 class="text-2xl font-extrabold">Página no encontrada</h1>
        </div>
        <p class="text-sm text-cero-muted mb-6">
            La ruta solicitada no existe o no está disponible.
        </p>
        <a href="/cero_project/public/dashboard" class="inline-flex items-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold px-5 py-3 rounded-xl transition shadow-sm">
            <i data-lucide="home" class="w-4 h-4"></i>
            Volver al Dashboard
        </a>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CERO | Registro de Pacientes</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
</head>
<body class="bg-cero-paper flex items-center justify-center min-h-screen p-6 font-sans relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute top-20 right-10 h-64 w-64 rounded-full bg-cero-sun/15 blur-3xl"></div>
        <div class="absolute bottom-10 left-10 h-72 w-72 rounded-full bg-cero-amber/10 blur-3xl"></div>
    </div>

    <div class="bg-white/95 backdrop-blur-md p-8 rounded-3xl shadow-cero-glow w-full max-w-2xl border border-cero-fog animate-cero-fade-up">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30 mb-4 mx-auto">
                <i data-lucide="clipboard-plus" class="w-7 h-7"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-cero-black tracking-tight">Crear Expediente Médico</h1>
            <p class="text-sm text-cero-muted mt-2">Portal exclusivo para pacientes y atletas.</p>
        </div>

        <form action="/cero_project/public/registro" method="POST" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Cédula de identidad:</label>
                    <input type="text" name="cedula" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Nombre completo:</label>
                    <input type="text" name="nombre_completo" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Fecha de nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>

                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Número telefónico:</label>
                    <input type="text" name="numero" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40" placeholder="0414-1234567">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Tipo de teléfono:</label>
                    <select name="tipo_telefono" class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none bg-white transition hover:border-cero-amber/40">
                        <option value="Móvil">Móvil</option>
                        <option value="Fijo">Fijo (Casa/Oficina)</option>
                    </select>
                </div>

                <div class="col-span-1 md:col-span-2 border-t border-cero-fog pt-6 mt-2">
                    <h3 class="text-lg font-bold text-cero-ink mb-4 flex items-center gap-2">
                        <i data-lucide="key" class="w-5 h-5 text-cero-honey"></i>
                        Datos de Acceso al Portal
                    </h3>
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Correo electrónico:</label>
                    <input type="email" name="correo" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Contraseña:</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40">
                </div>
            </div>
            
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-cero-black hover:bg-cero-ink text-cero-sun font-bold py-4 rounded-xl transition-all duration-200 shadow-md hover:shadow-cero transform hover:-translate-y-0.5 mt-6 border border-cero-line">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                Registrar Paciente
            </button>
        </form>
    </div>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

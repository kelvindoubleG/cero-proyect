<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Cita | CERO</title>
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
</head>
<body class="bg-cero-paper min-h-screen p-8 font-sans text-cero-ink relative">
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute top-20 right-10 h-48 w-48 rounded-full bg-cero-sun/10 blur-3xl"></div>
    </div>

    <div class="max-w-lg mx-auto bg-white/95 backdrop-blur-sm rounded-2xl border border-cero-fog shadow-cero-glow p-8 animate-cero-scale-in transition-all duration-300 hover:shadow-cero">
        <h2 class="text-2xl font-extrabold text-cero-black mb-2 flex items-center gap-2">
            <i data-lucide="calendar-plus" class="w-7 h-7 text-cero-honey"></i>
            Registrar nueva cita
        </h2>
        <p class="text-sm text-cero-muted mb-6">Formulario interno — personal autorizado.</p>
        <form action="/cero_project/public/cita/registrar" method="POST" class="space-y-4">
            <?php
            require_once __DIR__ . '/../../config/database.php';
            $db_conn = (new Database())->getConnection();
            require_once __DIR__ . '/../../models/Paciente.php';
            $pacientes = (new Paciente($db_conn))->obtenerTodos();
            ?>
            <div>
                <label class="block text-sm font-bold mb-1 flex items-center gap-2 text-cero-ink">
                    <i data-lucide="users" class="w-4 h-4 text-cero-honey"></i> Paciente:
                </label>
                <select name="id_paciente" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none bg-white transition hover:border-cero-amber/40">
                <?php foreach ($pacientes as $p): ?>
                    <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['nombre_completo']) ?></option>
                <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1 text-cero-ink">Fecha:</label>
                    <input type="date" name="fecha" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40" />
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 text-cero-ink">Hora:</label>
                    <input type="time" name="hora" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1 flex items-center gap-2 text-cero-ink">
                    <i data-lucide="align-left" class="w-4 h-4 text-cero-honey"></i> Motivo:
                </label>
                <textarea name="motivo" required rows="3" class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none transition hover:border-cero-amber/40"></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1 flex items-center gap-2 text-cero-ink">
                    <i data-lucide="briefcase" class="w-4 h-4 text-cero-honey"></i> Servicio:
                </label>
                <select name="id_servicio" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none bg-white transition hover:border-cero-amber/40">
                    <option value="1">Fisioterapia</option>
                    <option value="2">Acondicionamiento</option>
                    <option value="3">Nutrición</option>
                    <option value="4">Crioterapia</option>
                </select>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-cero-amber hover:bg-cero-honey text-cero-black font-bold py-3.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                <i data-lucide="save" class="w-4 h-4"></i> Guardar cita
            </button>
        </form>
    </div>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

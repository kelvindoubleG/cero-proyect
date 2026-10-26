<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CERO | Centro de Alto Rendimiento</title>
    <base href="/cero_project/public/">
    <?php require __DIR__ . '/../partials/tailwind_cero.php'; ?>
    <link rel="stylesheet" href="/cero_project/public/css/style.css">
    <style>
        /* Evita que .hero / .card-servicio de style.css pisen colores del hero (contraste garantizado) */
        #cero-landing-hero h1 { color: #0a0a0a !important; }
        #cero-landing-hero p { color: #525252 !important; }
        #cero-landing-hero .cero-hero-accent { color: #a16207 !important; }
        #cero-landing-hero a.cero-cta { color: #fde047 !important; }
        .cero-program-card h3 { color: #0a0a0a !important; }
        .cero-program-card p { color: #525252 !important; }
        html { scroll-padding-top: 5.5rem; }
        /* style.css global (intranet) afecta header/nav/main: neutralizar solo en esta pagina */
        #cero-landing-body > header#cero-landing-header {
            background-color: #0a0a0a !important;
            padding: 0 !important;
            border-bottom: 1px solid #404040 !important;
        }
        #cero-landing-body > header#cero-landing-header > div {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        #cero-landing-body > header#cero-landing-header nav {
            flex: none !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            padding: 0 !important;
            display: flex !important;
            width: auto !important;
            align-items: center !important;
            justify-content: flex-end !important;
        }
        #cero-landing-body > header#cero-landing-header nav a,
        #cero-landing-body > header#cero-landing-header nav button {
            width: auto !important;
            display: inline-flex !important;
            text-align: inherit !important;
        }
        #cero-landing-body > main {
            display: block !important;
            flex: none !important;
            overflow: visible !important;
            height: auto !important;
        }
    </style>
</head>
<body id="cero-landing-body" class="bg-cero-paper font-sans text-cero-black relative overflow-x-hidden">
    <!-- Fondo ambiental (marca amarillo / negro) -->
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute -top-32 -right-24 h-[min(70vw,520px)] w-[min(70vw,520px)] rounded-full bg-cero-sun/12 blur-3xl animate-cero-fade"></div>
        <div class="absolute top-1/2 -left-32 h-[min(55vw,420px)] w-[min(55vw,420px)] rounded-full bg-cero-amber/10 blur-3xl animate-cero-fade [animation-delay:120ms]"></div>
        <div class="absolute -bottom-24 right-1/4 h-64 w-64 rounded-full bg-cero-black/[0.03] blur-2xl"></div>
    </div>

    <header id="cero-landing-header" class="fixed w-full z-50 border-b border-cero-line shadow-lg transition-shadow duration-300 bg-cero-black text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 min-h-[4.25rem] flex items-center justify-between gap-3 py-2.5 md:min-h-[5rem] md:py-0">
            <a href="#cero-landing-hero" class="flex items-center gap-2 text-2xl sm:text-3xl font-black tracking-tighter text-white shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-cero-sun rounded-lg">
                <i data-lucide="activity" class="w-7 h-7 sm:w-8 sm:h-8 text-cero-sun shrink-0" aria-hidden="true"></i>
                <span class="text-white">CE<span class="text-cero-sun">RO</span></span>
            </a>
            <nav class="flex flex-wrap items-center justify-end gap-x-1 gap-y-2 sm:gap-2" aria-label="Principal">
                <a href="#cero-landing-hero" class="inline-flex items-center gap-2 font-bold text-white hover:text-cero-sun transition-colors duration-200 px-2.5 sm:px-3 py-2 rounded-lg hover:bg-white/10 text-sm sm:text-base">
                    <i data-lucide="home" class="w-4 h-4 text-cero-sun shrink-0" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Inicio</span>
                </a>
                <a href="#especialistas" class="inline-flex items-center gap-2 font-bold text-white hover:text-cero-sun transition-colors duration-200 px-2.5 sm:px-3 py-2 rounded-lg hover:bg-white/10 text-sm sm:text-base">
                    <i data-lucide="layers" class="w-4 h-4 text-cero-sun shrink-0" aria-hidden="true"></i>
                    <span>Programas</span>
                </a>
                <a href="/cero_project/public/login?info=Inicia%20Sesion%20como%20paciente%20para%20registrar%20una%20cita" class="inline-flex items-center gap-2 font-bold text-white hover:text-cero-sun transition-colors duration-200 px-2.5 sm:px-3 py-2 rounded-lg hover:bg-white/10 text-sm sm:text-base border-0 bg-transparent cursor-pointer">
                    <i data-lucide="calendar-plus" class="w-4 h-4 text-cero-sun shrink-0" aria-hidden="true"></i>
                    <span class="inline sm:hidden">Citas</span>
                    <span class="hidden sm:inline">Registro de Citas</span>
                </a>
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <span class="hidden md:inline max-w-[10rem] lg:max-w-[14rem] truncate text-xs font-semibold text-cero-soft px-1" title="<?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <a href="/cero_project/public/dashboard" class="inline-flex items-center justify-center gap-2 rounded-full border border-cero-sun/50 text-cero-sun px-3 sm:px-4 py-2 text-xs sm:text-sm font-bold hover:bg-white/10 transition-all duration-200 shrink-0">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
                        <span class="hidden sm:inline">Panel</span>
                    </a>
                    <a href="/cero_project/public/logout" class="inline-flex items-center justify-center gap-2 rounded-full bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 text-xs sm:text-sm font-bold border border-red-800/40 transition-all duration-200 shrink-0 shadow-sm">
                        <i data-lucide="log-out" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
                        <span class="hidden sm:inline">Salir</span>
                    </a>
                <?php else: ?>
                    <a href="/cero_project/public/login" class="inline-flex items-center justify-center gap-2 bg-cero-amber text-cero-black px-4 sm:px-6 py-2 sm:py-2.5 rounded-full font-bold text-xs sm:text-sm shadow-md hover:bg-cero-sun transition-all duration-200 hover:shadow-cero-glow hover:-translate-y-0.5 border border-cero-honey/40 shrink-0">
                        <i data-lucide="log-in" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
                        <span>Iniciar Sesión</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="pt-28 sm:pt-24 pb-20">
        <section id="cero-landing-hero" class="max-w-4xl mx-auto text-center px-6 mt-10 bg-white rounded-3xl shadow-cero p-12 sm:p-14 mx-4 border border-cero-fog animate-cero-scale-in text-cero-black">
            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6 text-cero-black">Precisión Científica para tu <span class="cero-hero-accent text-cero-honey">Salud Física.</span></h1>
            <p class="text-lg text-cero-muted mb-10 max-w-2xl mx-auto leading-relaxed">Clínica especializada en fisioterapia, nutrición y acondicionamiento en Cabudare. Gestiona tu recuperación de forma inteligente.</p>
            <a href="/cero_project/public/registro" class="cero-cta inline-flex items-center justify-center gap-2 bg-cero-black text-cero-sun px-10 py-4 rounded-xl font-bold text-lg hover:bg-cero-ink transition-all duration-300 shadow-lg hover:shadow-cero-glow hover:-translate-y-0.5 border border-cero-line">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                Regístrate como Paciente
            </a>
        </section>

        <section id="especialistas" class="max-w-7xl mx-auto px-6 mt-24 text-cero-black">
            <h2 class="text-3xl font-bold text-center mb-4 text-cero-black tracking-tight">Nuestros Programas de Atención</h2>
            <p class="text-center text-cero-ink/80 max-w-2xl mx-auto mb-12 text-sm leading-relaxed">Protocolos clínicos y deportivos con el mismo estándar visual y de calidad CERO.</p>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="group cero-program-card bg-white p-8 rounded-2xl border border-cero-fog hover:border-cero-amber/60 hover:shadow-cero transition-all duration-300 hover:-translate-y-1 text-left">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30 transition-transform duration-300 group-hover:scale-105">
                        <i data-lucide="heart-pulse" class="w-6 h-6" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-cero-black">Fisioterapia y Readaptación</h3>
                    <p class="text-cero-muted mb-4 leading-relaxed">Evaluación y tratamiento para recuperación de lesiones y mejora del rango de movimiento.</p>
                </div>
                <div class="group cero-program-card bg-white p-8 rounded-2xl border border-cero-fog hover:border-cero-amber/60 hover:shadow-cero transition-all duration-300 hover:-translate-y-1 text-left">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30 transition-transform duration-300 group-hover:scale-105">
                        <i data-lucide="dumbbell" class="w-6 h-6" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-cero-black">Acondicionamiento Físico</h3>
                    <p class="text-cero-muted mb-4 leading-relaxed">Planes de fuerza y resistencia estructurados por entrenadores certificados.</p>
                </div>
                <div class="group cero-program-card bg-white p-8 rounded-2xl border border-cero-fog hover:border-cero-amber/60 hover:shadow-cero transition-all duration-300 hover:-translate-y-1 text-left">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-cero-cream text-cero-honey ring-1 ring-cero-sun/30 transition-transform duration-300 group-hover:scale-105">
                        <i data-lucide="apple" class="w-6 h-6" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-cero-black">Crioterapia y Nutrición</h3>
                    <p class="text-cero-muted mb-4 leading-relaxed">Acelera tu regeneración celular y optimiza tu dieta con nuestros especialistas.</p>
                </div>
            </div>
        </section>
    </main>

    <div id="citaForm" class="fixed inset-0 bg-cero-black/55 backdrop-blur-sm flex items-center justify-center z-[60] hidden p-4 transition-opacity duration-300">
        <div class="bg-white p-8 rounded-3xl shadow-cero-glow w-full max-w-md border border-cero-fog animate-cero-scale-in">
            <div class="flex items-center justify-center gap-2 mb-6">
                <i data-lucide="calendar-check-2" class="w-7 h-7 text-cero-honey"></i>
                <h2 class="text-2xl font-bold text-cero-black">Registro de Cita</h2>
            </div>
            <form action="/cero_project/public/cita/publica" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Nombre:</label>
                    <input type="text" name="nombre" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none hover:border-cero-amber transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Apellido:</label>
                    <input type="text" name="apellido" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none hover:border-cero-amber transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Número de teléfono:</label>
                    <input type="text" name="telefono" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none hover:border-cero-amber transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Cédula:</label>
                    <input type="text" name="cedula" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none hover:border-cero-amber transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Fecha y hora:</label>
                    <input type="datetime-local" name="fecha_hora" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none hover:border-cero-amber transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-cero-ink mb-2">Área solicitada:</label>
                    <select name="area" required class="w-full px-4 py-3 rounded-xl border border-cero-fog focus:ring-2 focus:ring-cero-amber outline-none bg-white hover:border-cero-amber transition">
                        <option value="fisioterapia">Fisioterapia</option>
                        <option value="nutricion">Nutrición</option>
                    </select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-cero-amber text-cero-black font-bold py-3 rounded-xl hover:bg-cero-honey shadow-md transition-all duration-200 hover:-translate-y-0.5">
                        <i data-lucide="send" class="w-4 h-4"></i> Registrar
                    </button>
                    <button type="button" onclick="hideCitaForm()" class="flex-1 inline-flex items-center justify-center gap-2 bg-cero-mist text-cero-ink font-bold py-3 rounded-xl hover:bg-cero-fog transition shadow-md">
                        <i data-lucide="x" class="w-4 h-4"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCitaForm() {
            document.getElementById('citaForm').classList.remove('hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons({ attrs: { 'stroke-width': 1.65 } });
        }
        function hideCitaForm() {
            document.getElementById('citaForm').classList.add('hidden');
        }
    </script>
    <?php require __DIR__ . '/../partials/lucide_footer.php'; ?>
</body>
</html>

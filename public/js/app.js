document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.section-module');
    const headerTitle = document.querySelector('header h2');

    // Lógica de navegación del Dashboard
    if(buttons.length > 0) {
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                // Limpiar activos
                buttons.forEach(btn => btn.classList.remove('active'));
                sections.forEach(sec => sec.classList.remove('active'));

                // Activar seleccionado
                button.classList.add('active');
                
                // Mostrar sección
                const targetId = button.getAttribute('data-target');
                const targetSection = document.getElementById(targetId);
                if(targetSection) {
                    targetSection.classList.add('active');
                }
                
                // Cambiar título
                if(headerTitle) {
                    headerTitle.textContent = button.textContent;
                }
            });
        });
    }
});
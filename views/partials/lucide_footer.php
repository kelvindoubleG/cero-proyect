<?php
/** Lucide Icons (UMD). Incluir antes de </body>. Ejecuta createIcons al cargar el DOM. */
?>
<script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
<script>
(function () {
  function mountIcons() {
    if (typeof lucide === 'undefined') return;
    lucide.createIcons({ attrs: { 'stroke-width': 1.65 } });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountIcons);
  } else {
    mountIcons();
  }
})();
</script>

<?php
/**
 * Tailwind CDN + paleta CERO (negro / amarillo y subtonos).
 * Incluir dentro de <head> después de charset/title.
 */
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        cero: {
          black: '#0a0a0a',
          ink: '#171717',
          elevated: '#262626',
          line: '#404040',
          muted: '#737373',
          soft: '#a3a3a3',
          fog: '#e7e5e4',
          mist: '#f5f5f4',
          paper: '#fafaf8',
          cream: '#fffbeb',
          wheat: '#fef3c7',
          butter: '#fef08a',
          sun: '#fde047',
          gold: '#facc15',
          amber: '#eab308',
          honey: '#ca8a04',
          bronze: '#a16207',
          rust: '#854d0e',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        cero: '0 10px 40px -10px rgba(10, 10, 10, 0.25)',
        'cero-glow': '0 0 0 1px rgba(234, 179, 8, 0.12), 0 18px 50px -20px rgba(234, 179, 8, 0.25)',
      },
      keyframes: {
        'cero-fade-up': {
          '0%': { opacity: '0', transform: 'translateY(14px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        'cero-fade': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'cero-scale-in': {
          '0%': { opacity: '0', transform: 'scale(0.96)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
      },
      animation: {
        'cero-fade-up': 'cero-fade-up 0.55s cubic-bezier(0.22, 1, 0.36, 1) both',
        'cero-fade': 'cero-fade 0.45s ease-out both',
        'cero-scale-in': 'cero-scale-in 0.4s cubic-bezier(0.22, 1, 0.36, 1) both',
      },
      transitionTimingFunction: {
        'cero-out': 'cubic-bezier(0.22, 1, 0.36, 1)',
      },
    },
  },
};
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  /* Lucide: hereda color del contenedor (paleta CERO) */
  [data-lucide] { display: inline-block; vertical-align: middle; }
  .lucide { stroke: currentColor; }
</style>

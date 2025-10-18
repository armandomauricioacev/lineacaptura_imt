</div>
  </main>

  @php
    // PRODUCCIÓN v3 (oficial)
    $GOB_JS = 'https://framework-gb.cdn.gob.mx/gm/v3/assets/js/gobmx.js';
  @endphp

  {{-- JS oficial GOB.MX (PROD v3) --}}
  <script src="{{ $GOB_JS }}"></script>

  {{-- Scripts por-vista --}}
  @stack('scripts')

  <script>
    // Opcional: inicializaciones seguras (solo si $gmx existe)
    if (window.$gmx) {
      $gmx(function () {
        // …
      });
    }

    // 🔒 PREVENIR NAVEGACIÓN CON BOTONES DEL NAVEGADOR
    (function() {
      // Agregar entrada al historial para prevenir navegación hacia atrás
      if (window.history && window.history.pushState) {
        // Agregar estado actual al historial
        window.history.pushState(null, null, window.location.href);
        
        // Interceptar el evento popstate (botón atrás/adelante)
        window.addEventListener('popstate', function(event) {
          // Prevenir la navegación
          window.history.pushState(null, null, window.location.href);
          
          // Mostrar mensaje al usuario
          alert('Por favor, utiliza los botones del formulario para navegar entre las páginas.');
        });
      }

      // Prevenir atajos de teclado comunes para navegación
      document.addEventListener('keydown', function(event) {
        // Alt + Flecha izquierda (atrás)
        if (event.altKey && event.keyCode === 37) {
          event.preventDefault();
          alert('Por favor, utiliza los botones del formulario para navegar.');
          return false;
        }
        
        // Alt + Flecha derecha (adelante)
        if (event.altKey && event.keyCode === 39) {
          event.preventDefault();
          alert('Por favor, utiliza los botones del formulario para navegar.');
          return false;
        }
        
        // Backspace (en algunos navegadores funciona como atrás)
        if (event.keyCode === 8 && event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
          event.preventDefault();
          return false;
        }
      });

      // Nota: Se removió el bloqueo del clic derecho para permitir inspección durante desarrollo
      // Nota: Se removió el mensaje de confirmación beforeunload para mejorar la experiencia del usuario
    })();
  </script>
</body>
</html>

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
  </script>
</body>
</html>

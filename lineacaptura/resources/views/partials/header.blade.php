@php
  // PRODUCCIÓN v3 (oficial)
  $GOB_CSS = 'https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css';
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'Línea de Captura')</title>
  <link rel="icon" href="/favicon.ico">

  {{-- CSS oficial GOB.MX (PROD v3) --}}
  <link rel="stylesheet" href="{{ $GOB_CSS }}">

  {{-- Fuente Montserrat --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    /* Solo tipografía, sin alterar colores del framework */
    body, h1, h2, h3, h4, h5, h6 {
      font-family: "Montserrat", "Open Sans", Arial, sans-serif;
    }

    @media (max-width: 991px) {
  .navbar-toggler {
    align-self: flex-start !important;
    margin-top: -4px !important;
  }
  
  /* Opción 1: SVG inline con color blanco */
  .navbar-toggler .navbar-toggler-icon,
  button.navbar-toggler .navbar-toggler-icon,
  span.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='white' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
  }
}
  </style>
</head>
<body>
  <!-- gobmx.js inyecta header/pie institucionales; el script va en el footer -->
  <main class="page">
    <div class="container">

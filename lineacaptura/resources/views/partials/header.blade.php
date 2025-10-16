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
    /* 🎨 VARIABLES DE COLOR PARA FÁCIL ACCESO */
    :root {
        --color-dorado-hover: #EABE3F; /* Tono dorado para hover/active */
    }
  
    /* Solo tipografía, sin alterar colores del framework */
    body, h1, h2, h3, h4, h5, h6 {
      font-family: "Montserrat", "Open Sans", Arial, sans-serif;
    }

    /* Eliminar TODO efecto hover/focus en header gob.mx */
    header.main-header a:hover,
    header.main-header a:focus,
    header.main-header button:hover,
    header.main-header button:focus,
    .navbar a:hover,
    .navbar a:focus,
    .navbar button:hover,
    .navbar button:focus,
    .navbar-collapse a:hover,
    .navbar-collapse a:focus,
    .nav-item a:hover,
    .nav-item a:focus,
    .nav-link:hover,
    .nav-link:focus {
      background-color: transparent !important;
      background: transparent !important;
      box-shadow: none !important;
      border: none !important;
      outline: none !important;
    }

    /* Eliminar subrayado en TODOS los estados de los enlaces del header */
    header.main-header a,
    .navbar a,
    .navbar-nav .nav-link,
    .navbar-nav .btn,
    header a,
    header button {
      text-decoration: none !important;
    }

    header.main-header a:hover,
    header.main-header a:active,
    header.main-header a:focus,
    header.main-header a:visited,
    .navbar a:hover,
    .navbar a:active,
    .navbar a:focus,
    .navbar a:visited,
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link:active,
    .navbar-nav .nav-link:focus,
    .navbar-nav .nav-link:visited {
      text-decoration: none !important;
    }

    /* Eliminar estilos de botones específicos del framework */
    .btn-link:hover,
    .btn-link:focus,
    .btn-default:hover,
    .btn-default:focus {
      background-color: transparent !important;
      background: transparent !important;
      box-shadow: none !important;
      border: none !important;
    }

    /* Forzar transparencia en TODOS los elementos del navbar */
    nav[role="navigation"] *:hover,
    nav[role="navigation"] *:focus {
      background-color: transparent !important;
      box-shadow: none !important;
    }

    /* === 🖥️ ESTILOS PARA VISTA DE ESCRITORIO (PC) === */
    @media (min-width: 992px) {
        /* Aplica el color dorado al pasar el cursor sobre los elementos del menú */
        .navbar-nav > .nav-item:hover > .nav-link,
        .navbar-nav > .nav-item:hover > .btn {
            color: var(--color-dorado-hover) !important;
            transition: color 0.2s ease-in-out;
        }

        /* Eliminar subrayado al hacer clic en los elementos del menú */
        .navbar-nav > .nav-item > .nav-link:active,
        .navbar-nav > .nav-item > .btn:active,
        .navbar-nav > .nav-item > .nav-link:focus,
        .navbar-nav > .nav-item > .btn:focus {
            color: var(--color-dorado-hover) !important;
            text-decoration: none !important;
        }
    }

    /* === 📱 ESTILOS PARA VISTA MÓVIL === */
    @media (max-width: 991px) {
      .navbar-toggler {
        align-self: flex-start !important;
        margin-top: -4px !important;
      }
      
      /* Icono por defecto - 3 líneas horizontales */
      .navbar-toggler .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='white' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        transition: transform 0.3s ease-in-out, background-image 0.3s ease-in-out !important;
      }

      /* Cuando el menú está abierto (sin clase collapsed) - X con rotación */
      .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
        transform: rotate(180deg) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='white' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M6 6L24 24M6 24L24 6'/%3e%3c/svg%3e") !important;
      }

      /* 1. Forzar color blanco inicial para todos los elementos del menú */
      .navbar-nav .nav-link,
      .navbar-nav .btn {
          color: #ffffff !important;
      }

      /* 2. Aplicar color dorado solo al presionar (tap) */
      .navbar-nav .nav-link:active,
      .navbar-nav .btn:active {
          color: var(--color-dorado-hover) !important;
      }
    }

    /* === 🦶 ESTILOS PARA FOOTER === */
    
    /* Eliminar recuadro blanco al hacer click en enlaces del footer */
    footer a,
    footer a:focus,
    footer a:active,
    footer a:visited,
    footer .accordion a,
    footer .accordion a:focus,
    footer .accordion a:active,
    footer .nav-list a,
    footer .nav-list a:focus,
    footer .nav-list a:active {
      outline: none !important;
      border: none !important;
      box-shadow: none !important;
    }

    /* Eliminar recuadro en labels del acordeón */
    footer .accordion label,
    footer .accordion label:focus,
    footer .accordion label:active,
    footer label[for^="toggle"],
    footer label[for^="toggle"]:focus,
    footer label[for^="toggle"]:active {
      outline: none !important;
      border: none !important;
      box-shadow: none !important;
    }

    /* Eliminar recuadro en el input del acordeón */
    footer .accordion-toggle,
    footer .accordion-toggle:focus,
    footer .accordion-toggle:active,
    footer input[type="checkbox"],
    footer input[type="checkbox"]:focus {
      outline: none !important;
      border: none !important;
      box-shadow: none !important;
    }
    
    /* Quitar cursor pointer de Enlaces y ¿Qué es gob.mx? SOLO EN PC */
    @media (min-width: 992px) {
      footer .accordion label,
      footer .accordion label h5,
      footer .accordion label h3,
      footer .accordion-toggle + label,
      footer label[for^="toggle"],
      footer .sitemap-list .sitemap-item-title,
      footer .sitemap-list h3,
      footer h3.sitemap-item-title,
      footer .sitemap-item-title,
      footer .sitemap h3,
      footer h3 {
        cursor: default !important;
      }

      /* Deshabilitar clicks en los labels de acordeón SOLO EN PC */
      footer .accordion label {
        pointer-events: none !important;
      }
    }

    /* EN MÓVIL: Mantener funcionalidad normal del acordeón */
    @media (max-width: 991px) {
      footer .accordion label {
        cursor: pointer !important;
        pointer-events: auto !important;
      }
    }

    /* Permitir clicks en los enlaces en TODAS las vistas */
    footer .accordion a,
    footer a {
      pointer-events: auto !important;
      cursor: pointer !important;
    }

    /* Color dorado en hover para los enlaces del footer - ESCRITORIO */
    @media (min-width: 992px) {
      footer a:hover,
      footer .sitemap-list a:hover,
      footer .sitemap-links a:hover {
        color: var(--color-dorado-hover) !important;
        transition: color 0.2s ease-in-out;
      }
    }

    /* Color dorado al presionar (tap) para los enlaces del footer - MÓVIL */
    @media (max-width: 991px) {
      footer a:active,
      footer .sitemap-list a:active,
      footer .sitemap-links a:active {
        color: var(--color-dorado-hover) !important;
      }
    }

    /* Asegurar que los títulos no se vean como enlaces */
    footer .sitemap-item-title:hover,
    footer h3:hover {
      color: inherit !important;
      text-decoration: none !important;
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="container">
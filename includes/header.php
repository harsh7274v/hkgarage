<?php
require_once __DIR__ . '/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="it" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' | Officina Meccanica Villa Landri'; ?></title>
  <meta name="description" content="HK Garage di Harshit & Karan. Riparazioni, manutenzione e assistenza auto a Villa Landri (BG).">
  
  <!-- Google Fonts: Montserrat (Industrial Automotive Headings) & Inter (Clean Readable Body) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#E63946',
            'brand-dark': '#C1121F',
            ink: '#0F0F10',
            'ink-light': '#1A1A1D',
            cream: '#F5F1EC',
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            display: ['Montserrat', 'Inter', 'system-ui', '-apple-system', 'sans-serif'],
          }
        }
      }
    }
  </script>
  
  <!-- Flatpickr CSS & JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_red.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://npmcdn.com/flatpickr/dist/l10n/it.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom Design System Styles -->
  <style>
    html {
      background-color: #070708;
      overscroll-behavior-y: none;
      scroll-behavior: smooth;
      scroll-padding-top: 100px;
    }

    /* High-End Scroll Reveal Animations */
    .reveal {
      opacity: 0;
      transform: translateY(35px);
      transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }
    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }
    .reveal-left {
      opacity: 0;
      transform: translateX(-40px);
      transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }
    .reveal-left.active {
      opacity: 1;
      transform: translateX(0);
    }
    .reveal-right {
      opacity: 0;
      transform: translateX(40px);
      transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }
    .reveal-right.active {
      opacity: 1;
      transform: translateX(0);
    }
    .reveal-scale {
      opacity: 0;
      transform: scale(0.92);
      transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }
    .reveal-scale.active {
      opacity: 1;
      transform: scale(1);
    }

    /* Stagger Delays */
    .delay-100 { transition-delay: 100ms; }
    .delay-200 { transition-delay: 200ms; }
    .delay-300 { transition-delay: 300ms; }
    .delay-400 { transition-delay: 400ms; }
    .delay-500 { transition-delay: 500ms; }
    body {
      background: #FDFCFA;
      color: #0F0F10;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .font-display {
      font-family: 'Bricolage Grotesque', sans-serif;
    }
    .bg-metallic {
      background: linear-gradient(145deg, #1F1F23 0%, #0F0F10 100%);
    }
    .bg-metallic-dark {
      background: linear-gradient(145deg, #141416 0%, #070708 100%);
    }
    .btn-metallic {
      background: linear-gradient(145deg, #222226 0%, #0F0F10 100%);
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    }
    .btn-metallic:hover {
      background: linear-gradient(145deg, #2A2A30 0%, #141416 100%);
      border-color: rgba(255, 255, 255, 0.25);
    }
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    ::-webkit-scrollbar {
      width: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #F5F1EC;
    }
    ::-webkit-scrollbar-thumb {
      background: #0F0F10;
      border-radius: 4px;
    }
    ::selection {
      background: #E63946;
      color: #FFFFFF;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    @keyframes float-slow {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-8px); }
    }
    @keyframes fade-in {
      from { opacity: 0; transform: scale(0.98); }
      to { opacity: 1; transform: scale(1); }
    }
    @keyframes marquee {
      0% { transform: translateX(0%); }
      100% { transform: translateX(-50%); }
    }
    @keyframes marquee-reverse {
      0% { transform: translateX(-50%); }
      100% { transform: translateX(0%); }
    }
    .animate-float {
      animation: float 4s ease-in-out infinite;
    }
    .animate-float-slow {
      animation: float-slow 5s ease-in-out infinite;
      animation-delay: 0.5s;
    }
    .animate-fade-in {
      animation: fade-in 0.25s ease-out forwards;
    }
    .animate-marquee {
      display: flex;
      width: max-content;
      animation: marquee 35s linear infinite;
    }
    .animate-marquee-reverse {
      display: flex;
      width: max-content;
      animation: marquee-reverse 40s linear infinite;
    }
    .animate-marquee:hover, .animate-marquee-reverse:hover {
      animation-play-state: paused;
    }
  </style>
</head>
<body class="bg-[#FDFCFA] text-ink min-h-screen flex flex-col justify-between antialiased">

  <!-- Fixed Glassmorphism Header Navigation -->
  <header id="mainHeader" class="fixed top-0 left-0 right-0 z-50 transition-[background,box-shadow,backdrop-filter] duration-300 bg-white/90 backdrop-blur-xl border-b border-ink/5 shadow-sm">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      <div class="grid grid-cols-[auto_1fr_auto] items-center h-20 gap-4">
        
        <!-- Logo -->
        <a href="index.php#home" class="flex items-center gap-3 group">
          <div class="relative w-[72px] h-[52px] flex items-center justify-center rounded-xl overflow-hidden shadow-md border border-ink/20 bg-black flex-shrink-0 transition-transform group-hover:scale-105">
            <img src="https://i.ibb.co/JFR6RtjJ/PHOTO-2026-05-13-22-59-50.jpg" alt="Logo HK Garage" class="w-full h-full object-contain bg-black">
          </div>
          <div class="leading-tight">
            <div class="font-black text-ink text-[15px] tracking-tight font-display">HK GARAGE</div>
            <div class="text-[10px] font-semibold text-brand tracking-[0.15em]">HARSHIT & KARAN</div>
          </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center justify-center gap-0.5">
          <a href="index.php#home" class="px-3 py-2 text-sm font-semibold text-ink/70 hover:text-ink rounded-lg hover:bg-ink/5 transition-colors whitespace-nowrap">Home</a>
          <a href="index.php#servizi" class="px-3 py-2 text-sm font-semibold text-ink/70 hover:text-ink rounded-lg hover:bg-ink/5 transition-colors whitespace-nowrap">Servizi</a>
          <a href="index.php#chi-siamo" class="px-3 py-2 text-sm font-semibold text-ink/70 hover:text-ink rounded-lg hover:bg-ink/5 transition-colors whitespace-nowrap">Chi Siamo</a>
          <a href="index.php#galleria" class="px-3 py-2 text-sm font-semibold text-ink/70 hover:text-ink rounded-lg hover:bg-ink/5 transition-colors whitespace-nowrap">Galleria</a>
          <a href="index.php#recensioni" class="px-3 py-2 text-sm font-semibold text-ink/70 hover:text-ink rounded-lg hover:bg-ink/5 transition-colors whitespace-nowrap">Recensioni</a>
          <a href="news.php" class="px-3 py-2 text-sm font-bold text-brand hover:text-brand-dark rounded-lg hover:bg-brand/5 transition-colors flex items-center gap-1.5 whitespace-nowrap">
            <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span> News
          </a>
          <a href="contact.php" class="px-3 py-2 text-sm font-semibold text-ink/70 hover:text-ink rounded-lg hover:bg-ink/5 transition-colors whitespace-nowrap">Contatti</a>
        </nav>

        <!-- Right Header Actions -->
        <div class="hidden lg:flex items-center gap-4">
          <a href="tel:+393202819584" class="flex items-center gap-2 text-sm font-bold text-ink hover:text-brand transition-colors group">
            <img src="assets/logos/contact.webp" alt="Telefono HK Garage" class="w-9 h-9 object-contain transition-transform group-hover:scale-105">
            <span>320 281 9584</span>
          </a>
          <button onclick="openBookingModal()" class="btn-metallic text-white rounded-full px-6 h-11 font-semibold text-sm transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
            <i class="fa-solid fa-calendar-check text-xs text-brand"></i>
            Prenota ora
          </button>
        </div>

        <!-- Mobile Toggle Button -->
        <button id="menuToggle" class="lg:hidden p-2 rounded-lg text-ink hover:bg-ink/5 transition-colors" aria-label="Menu">
          <i class="fa-solid fa-bars text-xl" id="menuIcon"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="navMobile" class="hidden lg:hidden bg-white border-t border-ink/10 animate-fade-in shadow-xl max-h-[80vh] overflow-y-auto no-scrollbar">
      <div class="px-5 py-4 flex flex-col gap-1">
        <a href="index.php#home" class="px-3 py-3 text-base font-medium text-ink hover:bg-ink/5 rounded-lg">Home</a>
        <a href="index.php#servizi" class="px-3 py-3 text-base font-medium text-ink hover:bg-ink/5 rounded-lg">Servizi</a>
        <a href="index.php#chi-siamo" class="px-3 py-3 text-base font-medium text-ink hover:bg-ink/5 rounded-lg">Chi Siamo</a>
        <a href="index.php#galleria" class="px-3 py-3 text-base font-medium text-ink hover:bg-ink/5 rounded-lg">Galleria</a>
        <a href="index.php#recensioni" class="px-3 py-3 text-base font-medium text-ink hover:bg-ink/5 rounded-lg">Recensioni</a>
        <a href="news.php" class="px-3 py-3 text-base font-bold text-brand hover:bg-brand/5 rounded-lg">News & Aggiornamenti</a>
        <a href="contact.php" class="px-3 py-3 text-base font-medium text-ink hover:bg-ink/5 rounded-lg">Contatti</a>
        <a href="tel:+393202819584" class="w-full bg-ink/5 hover:bg-ink/10 text-ink font-bold text-sm py-3 px-4 rounded-xl flex items-center justify-center gap-2 mt-2 transition-colors">
          <img src="assets/logos/contact.webp" alt="Telefono" class="w-5 h-5 object-contain">
          320 281 9584
        </a>
        <button onclick="openBookingModal()" class="w-full bg-brand text-white rounded-full mt-2 h-12 font-bold flex items-center justify-center gap-2 shadow-md">
          <i class="fa-solid fa-calendar-check"></i> Prenota ora
        </button>
        <a href="admin/login.php" class="text-center font-bold text-xs text-ink/50 uppercase tracking-wider block py-3 mt-2 border-t border-ink/10">
          <i class="fa-solid fa-user-lock mr-1"></i> Area Riservata Admin
        </a>
      </div>
    </div>
  </header>


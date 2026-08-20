<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$adminName = $_SESSION['admin_name'] ?? 'Admin HK Garage';
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Admin HK Garage' : 'Pannello Amministrazione | HK Garage'; ?></title>
  
  <!-- Google Fonts: Inter & Montserrat -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#E63946',
            ink: '#18181B',
            canvas: '#ECECEC',
            cardBg: '#FFFFFF',
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
            display: ['Montserrat', 'system-ui', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- DataTables CSS & JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

  <style>
    body { font-family: 'Inter', sans-serif; background-color: #ECECEC; color: #18181B; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 99px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #18181B !important; color: white !important; border: 0 !important; border-radius: 99px !important; }
  </style>
</head>
<body class="min-h-screen bg-[#ECECEC] p-3 lg:p-5 flex flex-col lg:flex-row gap-5">

  <!-- Floating Sidebar Navigation -->
  <aside class="w-full lg:w-64 bg-white rounded-[28px] border border-black/5 shadow-sm p-6 flex flex-col justify-between flex-shrink-0">
    <div>
      <!-- Brand Logo -->
      <div class="flex items-center justify-between mb-8 pb-4 border-b border-black/5">
        <a href="dashboard.php" class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-[#18181B] text-white flex items-center justify-center font-black font-display text-lg shadow-md">
            HK
          </div>
          <div>
            <h1 class="font-display font-black text-base tracking-tight text-ink">HK Garage</h1>
            <span class="text-[10px] font-bold text-ink/40 tracking-wider uppercase block">Admin Portal</span>
          </div>
        </a>
      </div>

      <!-- Main Navigation Menu -->
      <nav class="space-y-1.5 text-sm font-semibold">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 <?php echo ($currentAdminPage === 'dashboard.php') ? 'bg-[#18181B] text-white font-bold shadow-sm' : 'text-ink/65 hover:bg-black/5 hover:text-ink'; ?>">
          <i class="fa-solid fa-grid-2 text-base w-5 text-center"></i>
          <span>Dashboard</span>
        </a>

        <a href="appointments.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 <?php echo ($currentAdminPage === 'appointments.php' || $currentAdminPage === 'appointment-view.php') ? 'bg-[#18181B] text-white font-bold shadow-sm' : 'text-ink/65 hover:bg-black/5 hover:text-ink'; ?>">
          <i class="fa-solid fa-calendar-check text-base w-5 text-center"></i>
          <span>Appuntamenti</span>
        </a>

        <a href="news.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 <?php echo ($currentAdminPage === 'news.php' || $currentAdminPage === 'news-add.php' || $currentAdminPage === 'news-edit.php') ? 'bg-[#18181B] text-white font-bold shadow-sm' : 'text-ink/65 hover:bg-black/5 hover:text-ink'; ?>">
          <i class="fa-solid fa-newspaper text-base w-5 text-center"></i>
          <span>News & Offerte</span>
        </a>

        <a href="../index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-ink/65 hover:bg-black/5 hover:text-ink transition-all duration-200">
          <i class="fa-solid fa-[#18181B] fa-arrow-up-right-from-square text-base w-5 text-center"></i>
          <span>Vedi Sito</span>
        </a>
      </nav>


    </div>

    <!-- Sidebar Footer Menu -->
    <div class="mt-8 pt-4 border-t border-black/5 space-y-2">
      <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-ink/65 hover:text-ink transition-colors">
        <i class="fa-solid fa-gear text-base w-5 text-center"></i>
        <span>Impostazioni</span>
      </a>
      <a href="logout.php" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-rose-600 hover:text-rose-700 transition-colors">
        <i class="fa-solid fa-right-from-bracket text-base w-5 text-center"></i>
        <span>Disconnetti</span>
      </a>
    </div>
  </aside>

  <!-- Main Dashboard View Container -->
  <div class="flex-1 flex flex-col min-w-0 space-y-6">

    <!-- Top Header Bar with Title, Time Filters, Search Bar & Notification Bell in ONE Line -->
    <header class="flex flex-row items-center justify-between gap-4 flex-nowrap overflow-x-auto custom-scrollbar pb-1">
      


      <!-- Header Controls: Time Filter Pills, Search Bar, Notifications in Single Row -->
      <div class="flex items-center gap-3 flex-shrink-0 flex-nowrap">
        
        <!-- Day / Week / Month / Year Segmented Control -->
        <div class="inline-flex bg-white rounded-full p-1 border border-black/5 shadow-sm text-xs font-bold text-ink/70 flex-shrink-0">
          <button type="button" class="px-3.5 py-1.5 rounded-full hover:text-ink transition-colors">Giorno</button>
          <button type="button" class="px-3.5 py-1.5 rounded-full hover:text-ink transition-colors">Settimana</button>
          <button type="button" class="px-3.5 py-1.5 rounded-full bg-[#18181B] text-white shadow-sm">Mese</button>
          <button type="button" class="px-3.5 py-1.5 rounded-full hover:text-ink transition-colors">Anno</button>
        </div>

        <!-- Custom Date Range Pill -->
        <div class="hidden xl:inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 border border-black/5 shadow-sm text-xs font-bold text-ink/70 flex-shrink-0 whitespace-nowrap">
          <i class="fa-regular fa-calendar text-ink/40"></i>
          <span><?php echo date('01 M Y') . ' - ' . date('t M Y'); ?></span>
        </div>

        <!-- Search Input Bar -->
        <div class="relative flex-shrink-0">
          <input type="text" placeholder="Cerca..." class="bg-white text-xs font-medium rounded-full pl-9 pr-4 py-2 border border-black/5 shadow-sm focus:border-ink outline-none w-36 sm:w-48 lg:w-56" />
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-ink/40"></i>
        </div>

        <!-- Notifications Bell -->
        <button class="w-9 h-9 rounded-full bg-white border border-black/5 shadow-sm flex items-center justify-center text-ink/70 hover:text-ink relative flex-shrink-0">
          <i class="fa-regular fa-bell text-sm"></i>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-brand"></span>
        </button>

      </div>
    </header>

    <!-- Main Content Area -->
    <main class="space-y-6">


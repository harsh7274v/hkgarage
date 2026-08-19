<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$adminName = $_SESSION['admin_name'] ?? 'Admin';
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Admin HK Garage' : 'Pannello Amministrazione | HK Garage'; ?></title>
  
  <!-- Google Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brandRed: '#d32f2f',
            brandRedDark: '#b71c1c',
            brandDark: '#1a1c1e',
          },
          fontFamily: {
            sans: ['Montserrat', 'sans-serif'],
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
    body { font-family: 'Montserrat', sans-serif; background-color: #f4f6f9; color: #2c3136; }
    .fc-event { cursor: pointer; border-radius: 4px; border: 0; padding: 2px 4px; }
  </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

  <!-- Sidebar Navigation -->
  <aside class="w-full md:w-64 bg-[#1a1c1e] text-white flex-shrink-0 flex flex-col justify-between">
    <div>
      <!-- Brand Header -->
      <div class="p-6 border-b border-gray-800 flex items-center justify-between">
        <a href="dashboard.php" class="flex items-center gap-3">
          <div class="w-10 h-10 bg-[#d32f2f] rounded-lg flex items-center justify-center font-black text-xl text-white">
            HK
          </div>
          <div>
            <h1 class="font-extrabold uppercase text-sm leading-none">HK Garage</h1>
            <span class="text-[10px] text-red-500 font-bold uppercase tracking-wider">Admin Portal</span>
          </div>
        </a>
      </div>

      <!-- Navigation Links -->
      <nav class="p-4 space-y-1 text-sm font-semibold">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?php echo ($currentAdminPage === 'dashboard.php') ? 'bg-[#d32f2f] text-white font-extrabold' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
          <i class="fa-solid fa-chart-line w-5 text-center"></i> Dashboard
        </a>

        <a href="appointments.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?php echo ($currentAdminPage === 'appointments.php' || $currentAdminPage === 'appointment-view.php') ? 'bg-[#d32f2f] text-white font-extrabold' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
          <i class="fa-solid fa-calendar-check w-5 text-center"></i> Appuntamenti
        </a>

        <a href="news.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?php echo ($currentAdminPage === 'news.php' || $currentAdminPage === 'news-add.php' || $currentAdminPage === 'news-edit.php') ? 'bg-[#d32f2f] text-white font-extrabold' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
          <i class="fa-solid fa-newspaper w-5 text-center"></i> News Homepage
        </a>

        <div class="pt-4 border-t border-gray-800 my-2"></div>

        <a href="../index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
          <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i> Vedi Sito Pubblico
        </a>
      </nav>
    </div>

    <!-- Admin Footer Info -->
    <div class="p-4 border-t border-gray-800 bg-[#141517]">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 overflow-hidden">
          <div class="w-8 h-8 rounded-full bg-gray-700 text-gray-200 flex items-center justify-center font-bold text-xs">
            <i class="fa-solid fa-user"></i>
          </div>
          <div class="truncate">
            <p class="text-xs font-bold text-white truncate"><?php echo htmlspecialchars($adminName); ?></p>
            <p class="text-[10px] text-gray-400 truncate">Amministratore</p>
          </div>
        </div>
        <a href="logout.php" class="text-gray-400 hover:text-red-500 p-2 text-base transition-colors" title="Disconnetti">
          <i class="fa-solid fa-right-from-bracket"></i>
        </a>
      </div>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <div class="flex-1 flex flex-col min-w-0">
    <!-- Topbar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm">
      <div>
        <h2 class="text-xl font-black uppercase text-[#1a1c1e]">
          <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Pannello Amministrazione'; ?>
        </h2>
      </div>
      <div class="flex items-center gap-4 text-xs font-semibold">
        <span class="text-gray-500"><i class="fa-regular fa-clock mr-1 text-[#d32f2f]"></i> <?php echo date('d/m/Y H:i'); ?></span>
        <a href="logout.php" class="bg-red-50 hover:bg-red-100 text-[#d32f2f] px-3 py-1.5 rounded font-extrabold transition-colors">
          Esci <i class="fa-solid fa-right-from-bracket ml-1"></i>
        </a>
      </div>
    </header>

    <main class="p-6 md:p-8 flex-1 overflow-y-auto">

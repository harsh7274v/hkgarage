<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';

requireLogin();

$adminName = $_SESSION['admin_name'] ?? 'Admin HK Garage';
$currentAdminPage = basename($_SERVER['PHP_SELF']);

// Handle Notification Actions (Confirm, Cancel, Reschedule)
$notifFlashMsg = '';
$notifFlashError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['notif_action']) || isset($_POST['dash_action']))) {
    $notifAction = $_POST['notif_action'] ?? $_POST['dash_action'] ?? '';
    $notifAppId  = intval($_POST['notif_app_id'] ?? $_POST['app_id'] ?? 0);

    if ($notifAppId > 0) {
        try {
            $dbH = getDBConnection();
            if ($notifAction === 'confirm') {
                $stmt = $dbH->prepare("UPDATE appointments SET status = 'Confirmed' WHERE id = :id");
                $stmt->execute([':id' => $notifAppId]);
                $notifFlashMsg = "Appuntamento #$notifAppId confermato con successo!";
                sendBookingStatusUpdateEmail($notifAppId, 'Confirmed');
            } elseif ($notifAction === 'update_status') {
                $newStatus = $_POST['new_status'] ?? 'Confirmed';
                $stmt = $dbH->prepare("UPDATE appointments SET status = :status WHERE id = :id");
                $stmt->execute([':status' => $newStatus, ':id' => $notifAppId]);
                $notifFlashMsg = "Stato dell'appuntamento #$notifAppId aggiornato a '$newStatus'!";
                sendBookingStatusUpdateEmail($notifAppId, $newStatus);
            } elseif ($notifAction === 'cancel') {
                $stmt = $dbH->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = :id");
                $stmt->execute([':id' => $notifAppId]);
                $notifFlashMsg = "Appuntamento #$notifAppId annullato.";
                sendBookingStatusUpdateEmail($notifAppId, 'Cancelled');
            } elseif ($notifAction === 'reschedule') {
                $newDate = $_POST['new_date'] ?? '';
                $newTime = $_POST['new_time'] ?? '';
                $newStatus = $_POST['new_status'] ?? 'Confirmed';
                if (!empty($newDate) && !empty($newTime)) {
                    $stmt = $dbH->prepare("UPDATE appointments SET booking_date = :bdate, booking_time = :btime, status = :status WHERE id = :id");
                    $stmt->execute([
                        ':bdate'  => $newDate,
                        ':btime'  => $newTime,
                        ':status' => $newStatus,
                        ':id'     => $notifAppId
                    ]);
                    $notifFlashMsg = "Appuntamento #$notifAppId riprogrammato per il " . date('d/m/Y', strtotime($newDate)) . " @ " . date('H:i', strtotime($newTime)) . "!";
                    sendBookingStatusUpdateEmail($notifAppId, $newStatus, $newDate, $newTime);
                }
            }
        } catch (Exception $e) {
            $notifFlashError = "Errore: " . $e->getMessage();
        }
    }
}


// Fetch Pending Appointments for Notification Center
$pendingNotifications = [];
$pendingBadgeCount = 0;

try {
    $dbN = getDBConnection();
    $stmtN = $dbN->query("
        SELECT a.*, s.name as service_name 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        WHERE a.status = 'Pending' 
        ORDER BY a.created_at DESC LIMIT 10
    ");
    $pendingNotifications = $stmtN->fetchAll();
    $pendingBadgeCount = count($pendingNotifications);
} catch (Exception $e) {
    error_log("Notification fetch error: " . $e->getMessage());
}

// Period Date Helper Functions for Dashboard, Appointments, and News
if (!function_exists('getPeriodDateBounds')) {
    function getPeriodDateBounds(string $period): array {
        $today = date('Y-m-d');
        switch ($period) {
            case 'day':
                return [$today, $today];
            case 'week':
                $start = date('Y-m-d', strtotime('monday this week'));
                $end   = date('Y-m-d', strtotime('sunday this week'));
                return [$start, $end];
            case 'month':
                $start = date('Y-m-01');
                $end   = date('Y-m-t');
                return [$start, $end];
            case 'year':
                $start = date('Y-01-01');
                $end   = date('Y-12-31');
                return [$start, $end];
            case 'all':
            default:
                return ['1970-01-01', '2099-12-31'];
        }
    }
}

if (!function_exists('buildPeriodUrl')) {
    function buildPeriodUrl(string $p): string {
        $params = $_GET;
        $params['period'] = $p;
        return '?' . http_build_query($params);
    }
}

if (!function_exists('formatItalianDateStr')) {
    function formatItalianDateStr(string $dateStr = 'now', string $format = 'd M Y'): string {
        $monthsEnToIt = [
            'Jan' => 'Gen', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
            'May' => 'Mag', 'Jun' => 'Giu', 'Jul' => 'Lug', 'Aug' => 'Ago',
            'Sep' => 'Set', 'Oct' => 'Ott', 'Nov' => 'Nov', 'Dec' => 'Dic',
            'January' => 'Gennaio', 'February' => 'Febbraio', 'March' => 'Marzo',
            'April' => 'Aprile', 'May' => 'Maggio', 'June' => 'Giugno',
            'July' => 'Luglio', 'August' => 'Agosto', 'September' => 'Settembre',
            'October' => 'Ottobre', 'November' => 'Novembre', 'December' => 'Dicembre'
        ];
        $formatted = date($format, strtotime($dateStr));
        return strtr($formatted, $monthsEnToIt);
    }
}

$defaultPeriod = ($currentAdminPage === 'dashboard.php') ? 'month' : 'all';
$currentPeriod = $_GET['period'] ?? $defaultPeriod;
if (!in_array($currentPeriod, ['all', 'day', 'week', 'month', 'year'])) {
    $currentPeriod = $defaultPeriod;
}

switch ($currentPeriod) {
    case 'day':
        $dateRangeLabel = formatItalianDateStr('now', 'd M Y');
        break;
    case 'week':
        $dateRangeLabel = formatItalianDateStr('monday this week', 'd M') . ' - ' . formatItalianDateStr('sunday this week', 'd M Y');
        break;
    case 'year':
        $dateRangeLabel = '01 Gen ' . date('Y') . ' - 31 Dic ' . date('Y');
        break;
    case 'month':
        $dateRangeLabel = formatItalianDateStr('first day of this month', '01 M Y') . ' - ' . formatItalianDateStr('last day of this month', 't M Y');
        break;
    case 'all':
    default:
        $dateRangeLabel = 'Tutti i Periodi';
        break;
}
?>
<!DOCTYPE html>
<html lang="it" class="notranslate" translate="no">
<head>
  <meta charset="UTF-8">
  <meta name="google" content="notranslate">
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
<body class="min-h-screen bg-[#ECECEC] p-3 lg:p-5 flex flex-col lg:flex-row gap-5" onclick="closeNotifDropdown()">

<?php if (!empty($notifFlashMsg)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'success',
        title: 'Operazione Completata',
        text: <?php echo json_encode($notifFlashMsg); ?>,
        confirmButtonColor: '#18181B'
      });
    });
  </script>
<?php endif; ?>

<?php if (!empty($notifFlashError)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'error',
        title: 'Errore',
        text: <?php echo json_encode($notifFlashError); ?>,
        confirmButtonColor: '#18181B'
      });
    });
  </script>
<?php endif; ?>

  <!-- Floating Sidebar Navigation -->
  <aside class="w-full lg:w-64 bg-white rounded-[28px] border border-black/5 shadow-sm p-6 flex flex-col justify-between flex-shrink-0" onclick="event.stopPropagation()">
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

    <!-- Top Header Bar with Title, Time Filters, Search Bar & Interactive Notification Bell -->
    <header class="relative z-50 flex flex-row items-center justify-between gap-4 flex-wrap sm:flex-nowrap pb-1" onclick="event.stopPropagation()">
      
      <!-- Header Controls: Time Filter Pills, Search Bar, Notifications in Single Row -->
      <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap w-full justify-between">
        
        <!-- Time Filter Pills Row -->
        <div class="flex items-center gap-3 overflow-x-auto custom-scrollbar py-1">
          <!-- Tutti / Day / Week / Month / Year Segmented Control -->
          <div class="inline-flex bg-white rounded-full p-1 border border-black/5 shadow-sm text-xs font-bold text-ink/70 flex-shrink-0">
            <a href="<?php echo buildPeriodUrl('all'); ?>" class="px-3.5 py-1.5 rounded-full transition-colors <?php echo ($currentPeriod === 'all') ? 'bg-[#18181B] text-white shadow-sm font-bold' : 'hover:text-ink'; ?>">Tutti</a>
            <a href="<?php echo buildPeriodUrl('day'); ?>" class="px-3.5 py-1.5 rounded-full transition-colors <?php echo ($currentPeriod === 'day') ? 'bg-[#18181B] text-white shadow-sm font-bold' : 'hover:text-ink'; ?>">Giorno</a>
            <a href="<?php echo buildPeriodUrl('week'); ?>" class="px-3.5 py-1.5 rounded-full transition-colors <?php echo ($currentPeriod === 'week') ? 'bg-[#18181B] text-white shadow-sm font-bold' : 'hover:text-ink'; ?>">Settimana</a>
            <a href="<?php echo buildPeriodUrl('month'); ?>" class="px-3.5 py-1.5 rounded-full transition-colors <?php echo ($currentPeriod === 'month') ? 'bg-[#18181B] text-white shadow-sm font-bold' : 'hover:text-ink'; ?>">Mese</a>
            <a href="<?php echo buildPeriodUrl('year'); ?>" class="px-3.5 py-1.5 rounded-full transition-colors <?php echo ($currentPeriod === 'year') ? 'bg-[#18181B] text-white shadow-sm font-bold' : 'hover:text-ink'; ?>">Anno</a>
          </div>

          <!-- Custom Date Range Pill -->
          <div class="hidden xl:inline-flex items-center gap-2 bg-white rounded-full px-4 py-2 border border-black/5 shadow-sm text-xs font-bold text-ink/70 flex-shrink-0 whitespace-nowrap">
            <i class="fa-regular fa-calendar text-ink/40"></i>
            <span><?php echo htmlspecialchars($dateRangeLabel); ?></span>
          </div>
        </div>

        <!-- Search Bar & Notification Bell -->
        <div class="flex items-center gap-3 flex-shrink-0">
          <!-- Search Input Bar -->
          <div class="relative flex-shrink-0">
            <input type="text" placeholder="Cerca..." class="bg-white text-xs font-medium rounded-full pl-9 pr-4 py-2 border border-black/5 shadow-sm focus:border-ink outline-none w-36 sm:w-48 lg:w-56" />
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-xs text-ink/40"></i>
          </div>

          <!-- Interactive Notification Bell & Dropdown Container -->
          <div class="relative flex-shrink-0">
            <button type="button" id="notifBellBtn" onclick="toggleNotifDropdown(event)" class="w-9 h-9 rounded-full bg-white border border-black/5 shadow-sm flex items-center justify-center text-ink/70 hover:text-ink relative transition-transform active:scale-95">
              <i class="fa-regular fa-bell text-sm"></i>
              <?php if ($pendingBadgeCount > 0): ?>
                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-brand text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white shadow-sm animate-pulse">
                  <?php echo $pendingBadgeCount; ?>
                </span>
              <?php endif; ?>
            </button>

            <!-- Floating Notification Dropdown Box (Front Overlay Layer) -->
            <div id="notifDropdown" class="hidden absolute right-0 top-12 w-80 sm:w-96 bg-white rounded-3xl border border-black/10 shadow-2xl p-5 z-[200] text-ink animate-fade-in" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-3 pb-3 border-b border-black/5">
              <div class="flex items-center gap-2">
                <h4 class="font-display font-black text-sm text-[#18181B]">Prenotazioni In Attesa</h4>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                  <?php echo $pendingBadgeCount; ?> in sospeso
                </span>
              </div>
              <button type="button" onclick="closeNotifDropdown()" class="text-ink/40 hover:text-ink text-xs">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>

            <?php if (empty($pendingNotifications)): ?>
              <div class="py-6 text-center text-ink/40 text-xs font-semibold">
                <i class="fa-solid fa-circle-check text-emerald-500 text-2xl block mb-2"></i>
                Nessuna prenotazione in attesa di conferma.
              </div>
            <?php else: ?>
              <div class="max-h-80 overflow-y-auto custom-scrollbar space-y-3 pr-1">
                <?php foreach ($pendingNotifications as $notifApp): ?>
                  <div class="p-3.5 bg-[#F8F8F8] rounded-2xl border border-black/5 space-y-2">
                    
                    <div class="flex items-start justify-between">
                      <div>
                        <span class="font-display font-bold text-xs text-[#18181B] block">
                          <?php echo htmlspecialchars($notifApp['customer_name']); ?>
                        </span>
                        <span class="text-[11px] text-ink/60 block">
                          <?php echo htmlspecialchars($notifApp['service_name'] ?? 'Servizio'); ?> • <?php echo htmlspecialchars($notifApp['vehicle_brand'] . ' ' . $notifApp['vehicle_model']); ?> (<?php echo htmlspecialchars($notifApp['vehicle_registration']); ?>)
                        </span>
                      </div>
                      <span class="text-[10px] font-mono font-bold text-ink/50 bg-white px-2 py-0.5 rounded-full border border-black/5">
                        #<?php echo $notifApp['id']; ?>
                      </span>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] font-bold text-brand">
                      <i class="fa-regular fa-calendar-check"></i>
                      <span><?php echo date('d/m/Y', strtotime($notifApp['booking_date'])); ?> @ <?php echo date('H:i', strtotime($notifApp['booking_time'])); ?></span>
                    </div>

                    <!-- Quick Actions Row: Confirm, Modify Time, Cancel -->
                    <div class="pt-2 border-t border-black/5 flex items-center justify-between gap-1.5">
                      
                      <!-- Confirm Form -->
                      <form method="POST" action="" class="inline-block flex-1">
                        <input type="hidden" name="notif_action" value="confirm">
                        <input type="hidden" name="notif_app_id" value="<?php echo $notifApp['id']; ?>">
                        <button type="submit" class="w-full py-1.5 px-2 bg-[#18181B] hover:bg-black text-white font-bold rounded-xl text-[11px] shadow-sm transition-all text-center">
                          <i class="fa-solid fa-check mr-1 text-[10px]"></i>Conferma
                        </button>
                      </form>

                      <!-- Modify Date/Time Button -->
                      <button type="button" onclick="openRescheduleModal(<?php echo htmlspecialchars(json_encode($notifApp)); ?>)" class="flex-1 py-1.5 px-2 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-xl text-[11px] transition-all text-center">
                        <i class="fa-solid fa-pen-to-square mr-1 text-[10px]"></i>Modifica
                      </button>

                      <!-- Cancel Form -->
                      <form method="POST" action="" class="inline-block flex-1">
                        <input type="hidden" name="notif_action" value="cancel">
                        <input type="hidden" name="notif_app_id" value="<?php echo $notifApp['id']; ?>">
                        <button type="submit" class="w-full py-1.5 px-2 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 font-bold rounded-xl text-[11px] transition-all text-center">
                          <i class="fa-solid fa-xmark mr-1 text-[10px]"></i>Annulla
                        </button>
                      </form>

                    </div>

                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="mt-3 pt-3 border-t border-black/5 text-center">
              <a href="appointments.php?status=Pending" class="text-xs font-bold text-ink/60 hover:text-brand transition-colors">
                Vedi tutte le prenotazioni &rarr;
              </a>
            </div>
          </div>
        </div>

      </div>
    </header>

    <!-- Reschedule Modal Popup -->
    <div id="rescheduleNotifModal" class="fixed inset-0 z-[140] bg-black/60 backdrop-blur-md flex items-center justify-center p-4 hidden" onclick="closeRescheduleModal()">
      <div class="bg-white max-w-md w-full rounded-[28px] overflow-hidden shadow-2xl border border-black/10 text-ink font-sans p-6" onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-black/5">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold">
              <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 class="font-display font-black text-lg text-[#18181B]">Modifica Orario & Data</h3>
          </div>
          <button type="button" onclick="closeRescheduleModal()" class="text-ink/40 hover:text-ink text-sm">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <form method="POST" action="" class="space-y-4">
          <input type="hidden" name="notif_action" value="reschedule">
          <input type="hidden" id="reschedule_app_id" name="notif_app_id" value="">

          <!-- Appointment Target Info Summary -->
          <div class="p-3 bg-[#F8F8F8] rounded-2xl border border-black/5 text-xs space-y-1">
            <div class="font-bold text-[#18181B]" id="rescheduleCustomerName"></div>
            <div class="text-ink/60" id="rescheduleVehicleInfo"></div>
          </div>

          <!-- New Date Input -->
          <div>
            <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-1.5 font-display">Nuova Data Appuntamento</label>
            <input type="date" id="rescheduleDate" name="new_date" required class="w-full px-4 py-2.5 bg-[#F8F8F8] border border-black/10 rounded-2xl text-xs font-bold outline-none focus:border-[#18181B]">
          </div>

          <!-- New Time Input -->
          <div>
            <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-1.5 font-display">Nuovo Orario</label>
            <select id="rescheduleTime" name="new_time" required class="w-full px-4 py-2.5 bg-[#F8F8F8] border border-black/10 rounded-2xl text-xs font-bold outline-none focus:border-[#18181B] cursor-pointer">
              <option value="08:30">08:30</option>
              <option value="09:30">09:30</option>
              <option value="10:30">10:30</option>
              <option value="11:30">11:30</option>
              <option value="14:00">14:00</option>
              <option value="15:00">15:00</option>
              <option value="16:00">16:00</option>
              <option value="17:00">17:00</option>
            </select>
          </div>

          <!-- Action Status Select -->
          <div>
            <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-1.5 font-display">Stato Dopo Modifica</label>
            <select name="new_status" class="w-full px-4 py-2.5 bg-[#F8F8F8] border border-black/10 rounded-2xl text-xs font-bold outline-none focus:border-[#18181B] cursor-pointer">
              <option value="Confirmed" selected>Confirmed (Conferma e Salva)</option>
              <option value="Pending">Pending (Lascia in Attesa)</option>
            </select>
          </div>

          <!-- Submit Buttons -->
          <div class="pt-2 flex items-center gap-3">
            <button type="button" onclick="closeRescheduleModal()" class="flex-1 py-3 bg-black/5 hover:bg-black/10 text-ink font-bold rounded-2xl text-xs transition-colors">
              Annulla
            </button>
            <button type="submit" class="flex-1 py-3 bg-[#18181B] hover:bg-black text-white font-bold rounded-2xl text-xs shadow-md transition-all">
              Conferma Modifica
            </button>
          </div>

        </form>
      </div>
    </div>

    <script>
      function toggleNotifDropdown(e) {
        if (e) e.stopPropagation();
        const dd = document.getElementById('notifDropdown');
        if (dd) dd.classList.toggle('hidden');
      }

      function closeNotifDropdown() {
        const dd = document.getElementById('notifDropdown');
        if (dd) dd.classList.add('hidden');
      }

      function openRescheduleModal(app) {
        closeNotifDropdown();
        const modal = document.getElementById('rescheduleNotifModal');
        const idElem = document.getElementById('reschedule_app_id');
        const nameElem = document.getElementById('rescheduleCustomerName');
        const vehElem = document.getElementById('rescheduleVehicleInfo');
        const dateInput = document.getElementById('rescheduleDate');
        const timeInput = document.getElementById('rescheduleTime');

        if (idElem) idElem.value = app.id || '';
        if (nameElem) nameElem.textContent = (app.customer_name || '') + ' (#' + (app.id || '') + ')';
        if (vehElem) vehElem.textContent = (app.service_name || 'Servizio') + ' • ' + (app.vehicle_brand || '') + ' ' + (app.vehicle_model || '') + ' (' + (app.vehicle_registration || '') + ')';
        if (dateInput) dateInput.value = app.booking_date || '';
        if (timeInput) timeInput.value = app.booking_time || '09:00';

        if (modal) modal.classList.remove('hidden');
      }

      function closeRescheduleModal() {
        const modal = document.getElementById('rescheduleNotifModal');
        if (modal) modal.classList.add('hidden');
      }
    </script>

    <!-- Main Content Area -->
    <main class="space-y-6">



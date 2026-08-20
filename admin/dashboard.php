<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = "Dashboard";

$currentPeriod = $_GET['period'] ?? 'month';
if (!in_array($currentPeriod, ['day', 'week', 'month', 'year'])) {
    $currentPeriod = 'month';
}

$todayCount = 0;
$pendingCount = 0;
$completedCount = 0;
$totalAppointments = 0;
$totalRevenue = 0.00;
$newsCount = 0;
$recentAppointments = [];
$calendarEvents = [];
$chartData = [];
$weekDays = [];
$capacityPercent = 0;
$periodSubtitle = "Questo Mese";

try {
    $db = getDBConnection();

    // ── Period-Filtered Metrics & Bar Graph SQL Queries ─────────────────────
    if ($currentPeriod === 'day') {
        $periodSubtitle = "Oggi (" . date('d/m/Y') . ")";

        // 1. Today Total Revenue
        $stmtRev = $db->query("SELECT COALESCE(SUM(s.price), 0) FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.booking_date = CURDATE() AND a.status IN ('Confirmed', 'Completed')");
        $totalRevenue = (float)$stmtRev->fetchColumn();

        // 2. Today Active Appointments
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE booking_date = CURDATE() AND status != 'Cancelled'");
        $totalAppointments = (int)$stmt->fetchColumn();
        $todayCount = $totalAppointments;

        // 3. Today Completed
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE booking_date = CURDATE() AND status = 'Completed'");
        $completedCount = (int)$stmt->fetchColumn();

        // 4. Bar Graph: 6 Operating Hour Intervals (08:00, 10:00, 12:00, 14:00, 16:00, 18:00)
        $slots = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00'];
        foreach ($slots as $slot) {
            $slotHour = substr($slot, 0, 2);
            $stmtM = $db->prepare("
                SELECT COUNT(*) as cnt, COALESCE(SUM(s.price), 0) as rev 
                FROM appointments a 
                LEFT JOIN services s ON a.service_id = s.id 
                WHERE a.booking_date = CURDATE() 
                  AND HOUR(a.booking_time) BETWEEN :hrStart AND :hrEnd
                  AND a.status != 'Cancelled'
            ");
            $stmtM->execute([':hrStart' => intval($slotHour), ':hrEnd' => intval($slotHour) + 1]);
            $mRow = $stmtM->fetch();

            $chartData[] = [
                'label' => $slot,
                'count' => (int)($mRow['cnt'] ?? 0),
                'revenue' => (float)($mRow['rev'] ?? 0)
            ];
        }
    } elseif ($currentPeriod === 'week') {
        $periodSubtitle = "Questa Settimana";

        // 1. Week Revenue
        $stmtRev = $db->query("SELECT COALESCE(SUM(s.price), 0) FROM appointments a JOIN services s ON a.service_id = s.id WHERE YEARWEEK(a.booking_date, 1) = YEARWEEK(CURDATE(), 1) AND a.status IN ('Confirmed', 'Completed')");
        $totalRevenue = (float)$stmtRev->fetchColumn();

        // 2. Week Active Appointments
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE YEARWEEK(booking_date, 1) = YEARWEEK(CURDATE(), 1) AND status != 'Cancelled'");
        $totalAppointments = (int)$stmt->fetchColumn();

        // 3. Today Count
        $stmtT = $db->query("SELECT COUNT(*) FROM appointments WHERE booking_date = CURDATE() AND status != 'Cancelled'");
        $todayCount = (int)$stmtT->fetchColumn();

        // 4. Week Completed
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE YEARWEEK(booking_date, 1) = YEARWEEK(CURDATE(), 1) AND status = 'Completed'");
        $completedCount = (int)$stmt->fetchColumn();

        // 5. Bar Graph: 7 Days of Current Week (Lun, Mar, Mer, Gio, Ven, Sab, Dom)
        $itDaysMap = ['Mon'=>'Lun','Tue'=>'Mar','Wed'=>'Mer','Thu'=>'Gio','Fri'=>'Ven','Sat'=>'Sab','Sun'=>'Dom'];
        $mondayTimestamp = strtotime('monday this week');
        for ($d = 0; $d < 7; $d++) {
            $dayTime = strtotime("+$d days", $mondayTimestamp);
            $daySql = date('Y-m-d', $dayTime);
            $dName = date('D', $dayTime);
            $label = $itDaysMap[$dName] ?? $dName;

            $stmtM = $db->prepare("
                SELECT COUNT(*) as cnt, COALESCE(SUM(s.price), 0) as rev 
                FROM appointments a 
                LEFT JOIN services s ON a.service_id = s.id 
                WHERE a.booking_date = :bdate 
                  AND a.status != 'Cancelled'
            ");
            $stmtM->execute([':bdate' => $daySql]);
            $mRow = $stmtM->fetch();

            $chartData[] = [
                'label' => $label,
                'count' => (int)($mRow['cnt'] ?? 0),
                'revenue' => (float)($mRow['rev'] ?? 0)
            ];
        }
    } elseif ($currentPeriod === 'year') {
        $periodSubtitle = "Quest'Anno (" . date('Y') . ")";

        // 1. Year Revenue
        $stmtRev = $db->query("SELECT COALESCE(SUM(s.price), 0) FROM appointments a JOIN services s ON a.service_id = s.id WHERE YEAR(a.booking_date) = YEAR(CURDATE()) AND a.status IN ('Confirmed', 'Completed')");
        $totalRevenue = (float)$stmtRev->fetchColumn();

        // 2. Year Active Appointments
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE YEAR(booking_date) = YEAR(CURDATE()) AND status != 'Cancelled'");
        $totalAppointments = (int)$stmt->fetchColumn();

        // 3. Today Count
        $stmtT = $db->query("SELECT COUNT(*) FROM appointments WHERE booking_date = CURDATE() AND status != 'Cancelled'");
        $todayCount = (int)$stmtT->fetchColumn();

        // 4. Year Completed
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE YEAR(booking_date) = YEAR(CURDATE()) AND status = 'Completed'");
        $completedCount = (int)$stmt->fetchColumn();

        // 5. Bar Graph: 12 Months of Current Year
        $allMonthsMap = [
            1=>'Gen', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mag', 6=>'Giug',
            7=>'Lug', 8=>'Ago', 9=>'Sett', 10=>'Ott', 11=>'Nov', 12=>'Dic'
        ];
        $currentYear = date('Y');

        for ($mNum = 1; $mNum <= 12; $mNum++) {
            $mStr = sprintf('%02d', $mNum);
            $monthKey = "$currentYear-$mStr";
            $label = $allMonthsMap[$mNum];

            $stmtM = $db->prepare("
                SELECT COUNT(*) as cnt, COALESCE(SUM(s.price), 0) as rev 
                FROM appointments a 
                LEFT JOIN services s ON a.service_id = s.id 
                WHERE DATE_FORMAT(a.booking_date, '%Y-%m') = :mdate 
                  AND a.status != 'Cancelled'
            ");
            $stmtM->execute([':mdate' => $monthKey]);
            $mRow = $stmtM->fetch();

            $chartData[] = [
                'label' => $label,
                'count' => (int)($mRow['cnt'] ?? 0),
                'revenue' => (float)($mRow['rev'] ?? 0)
            ];
        }
    } else {
        // Default: 'month' (Mese)
        $currentPeriod = 'month';
        $periodSubtitle = "Questo Mese";

        // 1. Total Revenue
        $stmtRev = $db->query("SELECT COALESCE(SUM(s.price), 0) FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.status IN ('Confirmed', 'Completed')");
        $totalRevenue = (float)$stmtRev->fetchColumn();

        // 2. Total Active Appointments
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE status != 'Cancelled'");
        $totalAppointments = (int)$stmt->fetchColumn();

        // 3. Today Count
        $stmtT = $db->query("SELECT COUNT(*) FROM appointments WHERE booking_date = CURDATE() AND status != 'Cancelled'");
        $todayCount = (int)$stmtT->fetchColumn();

        // 4. Completed Appointments
        $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'");
        $completedCount = (int)$stmt->fetchColumn();

        // 5. Bar Graph: Last 6 Months
        $itMonthsMap = [
            'Jan'=>'Gen', 'Feb'=>'Feb', 'Mar'=>'Mar', 'Apr'=>'Apr', 
            'May'=>'Mag', 'Jun'=>'Giug', 'Jul'=>'Lug', 'Aug'=>'Ago', 
            'Sep'=>'Sett', 'Oct'=>'Ott', 'Nov'=>'Nov', 'Dec'=>'Dic'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $mTime = strtotime("first day of -$i month");
            $monthKey = date('Y-m', $mTime);
            $monthName = date('M', $mTime);
            $label = $itMonthsMap[$monthName] ?? $monthName;

            $stmtM = $db->prepare("
                SELECT COUNT(*) as cnt, COALESCE(SUM(s.price), 0) as rev 
                FROM appointments a 
                LEFT JOIN services s ON a.service_id = s.id 
                WHERE DATE_FORMAT(a.booking_date, '%Y-%m') = :mdate 
                  AND a.status != 'Cancelled'
            ");
            $stmtM->execute([':mdate' => $monthKey]);
            $mRow = $stmtM->fetch();

            $chartData[] = [
                'label' => $label,
                'count' => (int)($mRow['cnt'] ?? 0),
                'revenue' => (float)($mRow['rev'] ?? 0)
            ];
        }
    }

    $maxChartCount = max(array_column($chartData, 'count'));
    if ($maxChartCount <= 0) $maxChartCount = 1;

    // ── Common Queries ──────────────────────────────────────────────────────
    $stmtP = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'");
    $pendingCount = (int)$stmtP->fetchColumn();

    $stmtN = $db->query("SELECT COUNT(*) FROM news");
    $newsCount = (int)$stmtN->fetchColumn();

    $stmtRecent = $db->query("
        SELECT a.*, s.name as service_name, s.price as service_price
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        ORDER BY a.created_at DESC LIMIT 6
    ");
    $recentAppointments = $stmtRecent->fetchAll();

    // Current Week Days Strip Data
    $itDaysMap = ['Mon'=>'Lun','Tue'=>'Mar','Wed'=>'Mer','Thu'=>'Gio','Fri'=>'Ven','Sat'=>'Sab','Sun'=>'Dom'];
    for ($i = -2; $i <= 2; $i++) {
        $dt = strtotime("$i days");
        $dName = date('D', $dt);
        $dNum  = date('d', $dt);
        $weekDays[] = [
            'day' => $itDaysMap[$dName] ?? $dName,
            'num' => $dNum,
            'isToday' => ($i === 0)
        ];
    }

    // Workshop Capacity (Today's bookings out of 16 max daily slots)
    $dailyMaxSlots = 16;
    $capacityPercent = min(100, round(($todayCount / $dailyMaxSlots) * 100));

    // FullCalendar Events
    $stmtCal = $db->query("
        SELECT a.id, a.customer_name, a.booking_date, a.booking_time, a.status, s.name as service_name 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        WHERE a.status != 'Cancelled'
    ");
    $calRows = $stmtCal->fetchAll();

    foreach ($calRows as $row) {
        $color = '#f59e0b';
        if ($row['status'] === 'Confirmed') $color = '#18181B';
        if ($row['status'] === 'Completed') $color = '#10b981';

        $cleanDate = date('Y-m-d', strtotime($row['booking_date']));
        $cleanTime = !empty($row['booking_time']) ? date('H:i:s', strtotime($row['booking_time'])) : '09:00:00';

        $calendarEvents[] = [
            'id' => $row['id'],
            'title' => '#' . $row['id'] . ' ' . $row['customer_name'] . ' - ' . ($row['service_name'] ?? 'Servizio'),
            'start' => $cleanDate . 'T' . $cleanTime,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'textColor' => '#ffffff',
            'url' => 'appointment-view.php?id=' . $row['id']
        ];
    }

} catch (Exception $e) {
    error_log("Dashboard stats fetch error: " . $e->getMessage());
}

require_once __DIR__ . '/admin-header.php';
?>

<!-- 1. Top Metrics Grid Row (Period-Filtered Real SQL Metrics) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
  
  <!-- Hero Obsidian Metric Card (Total Revenue) -->
  <div class="bg-[#18181B] text-white rounded-3xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between">
    <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
    <div>
      <span class="text-xs font-bold text-white/60 uppercase tracking-wider block">Incasso Totale</span>
      <h3 class="font-display font-black text-3xl text-white mt-2">€<?php echo number_format($totalRevenue, 2, ',', '.'); ?></h3>
    </div>
    <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-400">
      <i class="fa-solid fa-chart-line"></i>
      <span><?php echo htmlspecialchars($periodSubtitle); ?></span>
    </div>
  </div>

  <!-- Active Bookings Metric Card -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-black/5 flex flex-col justify-between">
    <div>
      <span class="text-xs font-bold text-ink/50 uppercase tracking-wider block">Appuntamenti Attivi</span>
      <h3 class="font-display font-black text-3xl text-ink mt-2"><?php echo number_format($totalAppointments); ?></h3>
    </div>
    <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-600">
      <i class="fa-solid fa-calendar-check"></i>
      <span><?php echo htmlspecialchars($periodSubtitle); ?></span>
    </div>
  </div>

  <!-- Today's Appointments Metric Card -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-black/5 flex flex-col justify-between">
    <div>
      <span class="text-xs font-bold text-ink/50 uppercase tracking-wider block">Appuntamenti Oggi</span>
      <h3 class="font-display font-black text-3xl text-ink mt-2"><?php echo number_format($todayCount); ?></h3>
    </div>
    <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-ink/60">
      <i class="fa-solid fa-clock"></i>
      <span>Programmati per oggi</span>
    </div>
  </div>

  <!-- Completed Services Metric Card -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-black/5 flex flex-col justify-between">
    <div>
      <span class="text-xs font-bold text-ink/50 uppercase tracking-wider block">Interventi Completati</span>
      <h3 class="font-display font-black text-3xl text-ink mt-2"><?php echo number_format($completedCount); ?></h3>
    </div>
    <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-600">
      <i class="fa-solid fa-check-double"></i>
      <span><?php echo htmlspecialchars($periodSubtitle); ?></span>
    </div>
  </div>

</div>

<!-- 2. Middle Row: Period-Responsive Revenue Bar Chart & Workshop Capacity Gauge -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
  
  <!-- Monthly/Period Revenue Bar Chart (7 Cols) -->
  <div class="lg:col-span-7 bg-white rounded-3xl p-7 shadow-sm border border-black/5 flex flex-col justify-between space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-black text-lg text-ink">Andamento Incassi & Interventi</h3>
        <span class="text-xs text-ink/50 font-semibold">Visualizzazione: <strong class="text-brand uppercase"><?php echo htmlspecialchars($currentPeriod); ?></strong></span>
      </div>
      <a href="appointments.php" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-ink/60 transition-colors" title="Dettagli Appuntamenti">
        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
      </a>
    </div>

    <!-- Dynamic Bar Chart Driven by Selected Period SQL Data -->
    <div class="w-full pt-4">
      <div class="flex items-end justify-between gap-2 sm:gap-3 h-52 px-2 border-b border-black/5 pb-2 overflow-x-auto custom-scrollbar">
        <?php foreach ($chartData as $idx => $m): 
            $barPct = max(10, round(($m['count'] / $maxChartCount) * 100));
            $isLast = ($idx === count($chartData) - 1);
        ?>
          <div class="flex-1 flex flex-col items-center gap-2 group cursor-pointer min-w-[24px]" title="<?php echo htmlspecialchars($m['label']) . ': ' . $m['count'] . ' appuntamenti (€' . number_format($m['revenue'], 2, ',', '.') . ')'; ?>">
            <div class="w-full max-w-[42px] <?php echo $isLast ? 'bg-slate-400' : 'bg-[#18181B]'; ?> rounded-2xl transition-all duration-300 group-hover:bg-brand" style="height: <?php echo $barPct; ?>%;"></div>
            <span class="text-[11px] sm:text-xs <?php echo $isLast ? 'font-black text-ink' : 'font-bold text-ink/50'; ?> font-display"><?php echo htmlspecialchars($m['label']); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Interactive Calendar Strip & Workshop Capacity Gauge (5 Cols) -->
  <div class="lg:col-span-5 bg-white rounded-3xl p-7 shadow-sm border border-black/5 flex flex-col justify-between space-y-6">
    
    <!-- Mini Calendar Header & Dynamic Days Strip -->
    <div>
      <div class="flex items-center justify-between mb-4">
        <span class="font-display font-black text-base text-ink capitalize"><?php echo formatItalianDateStr('now', 'F Y'); ?></span>
        <span class="text-xs font-bold text-ink/40 uppercase font-display">Settimana Attuale</span>
      </div>

      <!-- Days Strip Row (Real Current Week) -->
      <div class="grid grid-cols-5 gap-2 text-center">
        <?php foreach ($weekDays as $wd): ?>
          <?php if ($wd['isToday']): ?>
            <div class="p-2 bg-[#18181B] text-white rounded-2xl text-xs font-bold shadow-md">
              <div><?php echo $wd['day']; ?></div>
              <div class="mt-1 font-black text-white"><?php echo $wd['num']; ?></div>
            </div>
          <?php else: ?>
            <div class="p-2 rounded-2xl text-xs font-bold text-ink/50">
              <div><?php echo $wd['day']; ?></div>
              <div class="mt-1 font-black text-ink"><?php echo $wd['num']; ?></div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Workshop Capacity Gauge Widget (Real Dynamic SQL Metric) -->
    <div class="pt-4 border-t border-black/5 flex items-center justify-between">
      <div>
        <span class="text-xs font-bold text-ink/50 uppercase tracking-wider block">Capacità Officina Oggi</span>
        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 mt-1">
          <i class="fa-solid fa-circle text-[8px]"></i>
          <span><?php echo $todayCount; ?> di 16 slot occupati</span>
        </div>
      </div>

      <!-- Circular Progress Ring -->
      <div class="relative w-16 h-16 flex items-center justify-center">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
          <path class="text-black/5" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
          <path class="text-[#18181B]" stroke-dasharray="<?php echo $capacityPercent; ?>, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
        </svg>
        <span class="absolute font-display font-black text-xs text-ink"><?php echo $capacityPercent; ?>%</span>
      </div>
    </div>

  </div>

</div>

<!-- 3. Bottom Table: Recent Bookings (Real Database Data Only) -->
<div class="bg-white rounded-3xl p-7 shadow-sm border border-black/5">
  
  <!-- Table Header Bar -->
  <div class="flex items-center justify-between mb-6 pb-4 border-b border-black/5">
    <h3 class="font-display font-black text-lg text-ink">Ultimi Appuntamenti</h3>
    <div class="flex items-center gap-2">
      <button type="button" onclick="reloadRecentAppointments()" class="w-8 h-8 rounded-full bg-black/5 hover:bg-[#18181B] hover:text-white flex items-center justify-center text-xs text-ink/60 transition-all shadow-sm" title="Aggiorna Elenco">
        <i id="reloadAppointmentsIcon" class="fa-solid fa-rotate-right"></i>
      </button>
      <a href="appointments.php" class="w-8 h-8 rounded-full bg-black/5 hover:bg-[#18181B] hover:text-white flex items-center justify-center text-xs text-ink/60 transition-all shadow-sm" title="Vedi Tutti">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </a>
    </div>
  </div>

  <!-- Recent Bookings Data Table -->
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="text-xs font-bold text-ink/40 uppercase border-b border-black/5 pb-3">
          <th class="pb-3 font-display">Servizio / Veicolo</th>
          <th class="pb-3 font-display">Nome Cliente</th>
          <th class="pb-3 font-display">ID</th>
          <th class="pb-3 font-display">Data & Ora</th>
          <th class="pb-3 font-display">Stato</th>
          <th class="pb-3 font-display text-right">Azioni</th>
        </tr>
      </thead>
      <tbody id="recentAppointmentsTbody" class="divide-y divide-black/5 font-medium text-ink">
        <?php if (empty($recentAppointments)): ?>
          <tr>
            <td colspan="6" class="py-8 text-center text-ink/40 font-semibold text-xs">
              Nessun appuntamento recente registrato nel database.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($recentAppointments as $app): ?>
            <tr class="hover:bg-black/[0.02] transition-colors">
              
              <!-- Service Name with Vehicle Info -->
              <td class="py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-[#18181B] text-white flex items-center justify-center text-base font-bold flex-shrink-0 shadow-sm">
                    <i class="fa-solid fa-wrench"></i>
                  </div>
                  <div>
                    <span class="font-display font-bold text-sm text-ink block"><?php echo htmlspecialchars($app['service_name'] ?? 'Servizio Personalizzato'); ?></span>
                    <span class="text-xs text-ink/50 font-normal"><?php echo htmlspecialchars($app['vehicle_brand'] . ' ' . $app['vehicle_model']); ?> (<?php echo htmlspecialchars($app['vehicle_registration']); ?>)</span>
                  </div>
                </div>
              </td>

              <!-- Customer Name -->
              <td class="py-4 font-semibold text-sm">
                <?php echo htmlspecialchars($app['customer_name']); ?>
              </td>

              <!-- Booking ID -->
              <td class="py-4 font-mono font-bold text-xs text-ink/70">
                #<?php echo $app['id']; ?>
              </td>

              <!-- Date & Time -->
              <td class="py-4 text-xs whitespace-nowrap">
                <div class="font-bold text-ink"><?php echo date('d/m/Y', strtotime($app['booking_date'])); ?></div>
                <div class="text-brand font-bold"><i class="fa-regular fa-clock mr-1"></i><?php echo date('H:i', strtotime($app['booking_time'])); ?></div>
              </td>

              <!-- Status Interactive Dropdown Selector -->
              <td class="py-4">
                <form method="POST" action="" class="inline-block">
                  <input type="hidden" name="dash_action" value="update_status">
                  <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                  <?php
                    $selectBg = 'bg-amber-100 text-amber-800';
                    if ($app['status'] === 'Confirmed') $selectBg = 'bg-[#18181B] text-white';
                    if ($app['status'] === 'Completed') $selectBg = 'bg-emerald-100 text-emerald-800';
                    if ($app['status'] === 'Cancelled') $selectBg = 'bg-rose-100 text-rose-800';
                  ?>
                  <select name="new_status" onchange="this.form.submit()" class="text-xs font-bold px-3 py-1.5 rounded-full shadow-sm cursor-pointer border-0 outline-none <?php echo $selectBg; ?>">
                    <option value="Pending" <?php echo ($app['status'] === 'Pending') ? 'selected' : ''; ?>>In Attesa</option>
                    <option value="Confirmed" <?php echo ($app['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confermato</option>
                    <option value="Completed" <?php echo ($app['status'] === 'Completed') ? 'selected' : ''; ?>>Completato</option>
                    <option value="Cancelled" <?php echo ($app['status'] === 'Cancelled') ? 'selected' : ''; ?>>Annullato</option>
                  </select>
                </form>
              </td>

              <!-- Table Action Buttons (Reschedule & Full Details) -->
              <td class="py-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button type="button" onclick="openRescheduleModal(<?php echo htmlspecialchars(json_encode($app)); ?>)" class="bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold px-3 py-1.5 rounded-full transition-colors" title="Modifica Data & Ora">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Modifica
                  </button>

                  <a href="appointment-view.php?id=<?php echo $app['id']; ?>" class="bg-black/5 hover:bg-[#18181B] hover:text-white text-ink text-xs font-bold px-3 py-1.5 rounded-full transition-colors" title="Visualizza Scheda">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                </div>
              </td>

            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- FullCalendar Modern Custom Theme Overrides -->
<style>
  #calendar {
    --fc-border-color: rgba(0, 0, 0, 0.06);
    --fc-button-bg-color: #18181B;
    --fc-button-border-color: #18181B;
    --fc-button-hover-bg-color: #000000;
    --fc-button-hover-border-color: #000000;
    --fc-button-active-bg-color: #E63946;
    --fc-button-active-border-color: #E63946;
    --fc-today-bg-color: rgba(230, 57, 70, 0.05);
  }
  .fc .fc-toolbar-title {
    font-family: 'Montserrat', sans-serif !important;
    font-weight: 900 !important;
    font-size: 1.2rem !important;
    color: #18181B !important;
    letter-spacing: -0.01em;
  }
  .fc .fc-button {
    border-radius: 9999px !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    padding: 7px 16px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.2s ease !important;
    text-transform: capitalize !important;
  }
  .fc .fc-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
  }
  .fc-theme-standard .fc-scrollgrid {
    border-radius: 20px !important;
    overflow: hidden !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
  }
  .fc .fc-col-header-cell {
    background-color: #F8F8F8 !important;
    padding: 12px 0 !important;
  }
  .fc .fc-col-header-cell-cushion {
    font-family: 'Montserrat', sans-serif !important;
    font-weight: 800 !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    color: rgba(24, 24, 27, 0.6) !important;
    text-decoration: none !important;
  }
  .fc .fc-daygrid-day-number {
    font-weight: 700 !important;
    font-size: 0.8rem !important;
    color: #18181B !important;
    padding: 6px 10px !important;
    text-decoration: none !important;
  }
  .fc .fc-day-today .fc-daygrid-day-number {
    background-color: #E63946 !important;
    color: #FFFFFF !important;
    border-radius: 9999px !important;
    box-shadow: 0 2px 6px rgba(230, 57, 70, 0.4);
  }
  .fc-daygrid-event {
    border-radius: 12px !important;
    padding: 4px 8px !important;
    margin-top: 3px !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    border: 0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease !important;
    cursor: pointer !important;
  }
  .fc-daygrid-event:hover {
    transform: translateY(-1px) scale(1.02) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
  }
  .fc-event-main {
    color: #FFFFFF !important;
  }
</style>

<!-- Modern FullCalendar Container -->
<div class="mt-8 bg-white rounded-3xl p-7 shadow-sm border border-black/5">
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 border-b border-black/5 pb-4">
    <div>
      <h3 class="font-display font-black text-lg text-ink flex items-center gap-2">
        <i class="fa-solid fa-calendar-days text-brand"></i> Calendario Completo Prenotazioni
      </h3>
      <p class="text-xs text-ink/50 font-medium">Visualizzazione interattiva mensile e settimanale degli interventi in officina</p>
    </div>
    <a href="appointments.php" class="bg-[#18181B] hover:bg-black text-white text-xs font-bold px-5 py-2.5 rounded-full shadow-sm transition-all hover:scale-105 flex-shrink-0">
      Gestisci Tutti &rarr;
    </a>
  </div>
  <div id="calendar" class="min-h-[460px] notranslate" translate="no"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');
  if (calendarEl) {
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'it',
      firstDay: 1, // Start week on Monday
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      buttonText: {
        today: 'Oggi',
        month: 'Mese',
        week: 'Settimana',
        day: 'Giorno'
      },
      events: <?php echo json_encode($calendarEvents); ?>,
      datesSet: function(info) {
        // Enforce clean single title and prevent browser translation duplication
        const toolbarTitle = calendarEl.querySelector('.fc-toolbar-title');
        if (toolbarTitle && info.view && info.view.title) {
          toolbarTitle.textContent = info.view.title;
        }
      },
      eventDidMount: function(info) {
        info.el.setAttribute('title', info.event.title);
      },
      eventClick: function(info) {
        if (info.event.url) {
          window.location.href = info.event.url;
          info.jsEvent.preventDefault();
        }
      }
    });
    calendar.render();
  }
});

function reloadRecentAppointments() {
  const icon = document.getElementById('reloadAppointmentsIcon');
  const tbody = document.getElementById('recentAppointmentsTbody');
  if (!icon || !tbody) return;

  icon.classList.add('fa-spin');

  fetch('../api/get-recent-appointments.php')
    .then(res => res.json())
    .then(data => {
      icon.classList.remove('fa-spin');
      if (data.success && Array.isArray(data.appointments)) {
        if (data.appointments.length === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="6" class="py-8 text-center text-ink/40 font-semibold text-xs">
                Nessun appuntamento recente registrato nel database.
              </td>
            </tr>
          `;
          return;
        }

        let html = '';
        data.appointments.forEach(app => {
          let selectBg = 'bg-amber-100 text-amber-800';
          if (app.status === 'Confirmed') selectBg = 'bg-[#18181B] text-white';
          if (app.status === 'Completed') selectBg = 'bg-emerald-100 text-emerald-800';
          if (app.status === 'Cancelled') selectBg = 'bg-rose-100 text-rose-800';

          const appJson = JSON.stringify({
            id: app.id,
            customer_name: app.customer_name,
            service_name: app.service_name,
            vehicle_brand: app.vehicle_brand,
            vehicle_model: app.vehicle_model,
            vehicle_registration: app.vehicle_registration,
            booking_date: app.raw_booking_date,
            booking_time: app.raw_booking_time,
            status: app.status
          }).replace(/"/g, '&quot;');

          html += `
            <tr class="hover:bg-black/[0.02] transition-colors">
              <td class="py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-[#18181B] text-white flex items-center justify-center text-base font-bold flex-shrink-0 shadow-sm">
                    <i class="fa-solid fa-wrench"></i>
                  </div>
                  <div>
                    <span class="font-display font-bold text-sm text-ink block">${escapeHtml(app.service_name)}</span>
                    <span class="text-xs text-ink/50 font-normal">${escapeHtml(app.vehicle_brand + ' ' + app.vehicle_model)} (${escapeHtml(app.vehicle_registration)})</span>
                  </div>
                </div>
              </td>
              <td class="py-4 font-semibold text-sm">${escapeHtml(app.customer_name)}</td>
              <td class="py-4 font-mono font-bold text-xs text-ink/70">#${app.id}</td>
              <td class="py-4 text-xs whitespace-nowrap">
                <div class="font-bold text-ink">${app.booking_date}</div>
                <div class="text-brand font-bold"><i class="fa-regular fa-clock mr-1"></i>${app.booking_time}</div>
              </td>
              <td class="py-4">
                <form method="POST" action="" class="inline-block">
                  <input type="hidden" name="dash_action" value="update_status">
                  <input type="hidden" name="app_id" value="${app.id}">
                  <select name="new_status" onchange="this.form.submit()" class="text-xs font-bold px-3 py-1.5 rounded-full shadow-sm cursor-pointer border-0 outline-none ${selectBg}">
                    <option value="Pending" ${app.status === 'Pending' ? 'selected' : ''}>In Attesa</option>
                    <option value="Confirmed" ${app.status === 'Confirmed' ? 'selected' : ''}>Confermato</option>
                    <option value="Completed" ${app.status === 'Completed' ? 'selected' : ''}>Completato</option>
                    <option value="Cancelled" ${app.status === 'Cancelled' ? 'selected' : ''}>Annullato</option>
                  </select>
                </form>
              </td>
              <td class="py-4 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                  <button type="button" onclick="openRescheduleModal(${appJson})" class="bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold px-3 py-1.5 rounded-full transition-colors" title="Modifica Data & Ora">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Modifica
                  </button>
                  <a href="appointment-view.php?id=${app.id}" class="bg-black/5 hover:bg-[#18181B] hover:text-white text-ink text-xs font-bold px-3 py-1.5 rounded-full transition-colors" title="Visualizza Scheda">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                </div>
              </td>
            </tr>
          `;
        });
        tbody.innerHTML = html;

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Elenco aggiornato con successo!',
          showConfirmButton: false,
          timer: 2000
        });
      }
    })
    .catch(err => {
      icon.classList.remove('fa-spin');
      console.error('Error reloading appointments:', err);
    });
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}
</script>

<?php require_once __DIR__ . '/admin-footer.php'; ?>



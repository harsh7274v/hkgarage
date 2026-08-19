<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = "Dashboard";

$todayCount = 0;
$pendingCount = 0;
$completedCount = 0;
$newsCount = 0;
$recentAppointments = [];
$calendarEvents = [];

try {
    $db = getDBConnection();

    // 1. Today's Appointments
    $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE booking_date = CURDATE() AND status != 'Cancelled'");
    $todayCount = (int)$stmt->fetchColumn();

    // 2. Pending Appointments
    $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'");
    $pendingCount = (int)$stmt->fetchColumn();

    // 3. Completed Appointments
    $stmt = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'Completed'");
    $completedCount = (int)$stmt->fetchColumn();

    // 4. News Count
    $stmt = $db->query("SELECT COUNT(*) FROM news");
    $newsCount = (int)$stmt->fetchColumn();

    // 5. Recent 5 Appointments
    $stmt = $db->query("
        SELECT a.*, s.name as service_name 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        ORDER BY a.created_at DESC LIMIT 5
    ");
    $recentAppointments = $stmt->fetchAll();

    // 6. Fetch all appointments for FullCalendar
    $stmtCal = $db->query("
        SELECT a.id, a.customer_name, a.booking_date, a.booking_time, a.status, s.name as service_name 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        WHERE a.status != 'Cancelled'
    ");
    $calRows = $stmtCal->fetchAll();

    foreach ($calRows as $row) {
        $color = '#f59e0b'; // Pending amber
        if ($row['status'] === 'Confirmed') $color = '#3b82f6'; // Blue
        if ($row['status'] === 'Completed') $color = '#10b981'; // Green

        $calendarEvents[] = [
            'id' => $row['id'],
            'title' => '#' . $row['id'] . ' ' . $row['customer_name'] . ' (' . $row['service_name'] . ')',
            'start' => $row['booking_date'] . 'T' . $row['booking_time'],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'url' => 'appointment-view.php?id=' . $row['id']
        ];
    }

} catch (Exception $e) {
    error_log("Dashboard stats fetch error: " . $e->getMessage());
}

require_once __DIR__ . '/admin-header.php';
?>

<!-- Dashboard Metrics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  
  <!-- Today's Appointments -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
    <div>
      <p class="text-xs font-extrabold uppercase text-gray-500 tracking-wider">Appuntamenti Oggi</p>
      <h3 class="text-3xl font-black text-[#1a1c1e] mt-1"><?php echo $todayCount; ?></h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
      <i class="fa-solid fa-calendar-day"></i>
    </div>
  </div>

  <!-- Pending Appointments -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
    <div>
      <p class="text-xs font-extrabold uppercase text-gray-500 tracking-wider">In Attesa (Pending)</p>
      <h3 class="text-3xl font-black text-amber-600 mt-1"><?php echo $pendingCount; ?></h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
      <i class="fa-solid fa-clock font-bold"></i>
    </div>
  </div>

  <!-- Completed Appointments -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
    <div>
      <p class="text-xs font-extrabold uppercase text-gray-500 tracking-wider">Completati</p>
      <h3 class="text-3xl font-black text-emerald-600 mt-1"><?php echo $completedCount; ?></h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
      <i class="fa-solid fa-circle-check"></i>
    </div>
  </div>

  <!-- News Published Count -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
    <div>
      <p class="text-xs font-extrabold uppercase text-gray-500 tracking-wider">News Pubblicate</p>
      <h3 class="text-3xl font-black text-[#d32f2f] mt-1"><?php echo $newsCount; ?></h3>
    </div>
    <div class="w-12 h-12 rounded-xl bg-red-50 text-[#d32f2f] flex items-center justify-center text-xl font-bold">
      <i class="fa-solid fa-newspaper"></i>
    </div>
  </div>

</div>

<!-- Calendar & Recent Appointments Split Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  
  <!-- Interactive Calendar (2 Columns) -->
  <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="flex items-center justify-between mb-4 border-b pb-3">
      <h3 class="font-extrabold text-base uppercase text-[#1a1c1e]">
        <i class="fa-solid fa-calendar-days text-[#d32f2f] mr-2"></i> Calendario Prenotazioni
      </h3>
      <a href="appointments.php" class="text-xs font-bold text-[#d32f2f] hover:underline">Gestisci Tutti &rarr;</a>
    </div>
    <div id="calendar" class="min-h-[400px]"></div>
  </div>

  <!-- Recent Appointments Sidebar (1 Column) -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between">
    <div>
      <div class="flex items-center justify-between mb-4 border-b pb-3">
        <h3 class="font-extrabold text-base uppercase text-[#1a1c1e]">Ultimi Inserimenti</h3>
        <a href="appointments.php" class="text-xs text-gray-500 font-bold hover:text-[#d32f2f]">Vedi Tutti</a>
      </div>

      <?php if (empty($recentAppointments)): ?>
        <p class="text-xs text-gray-400 font-semibold text-center py-8">Nessun appuntamento registrato.</p>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($recentAppointments as $app): ?>
            <div class="p-3 bg-gray-50 rounded-lg border hover:border-gray-300 transition-colors">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-black text-[#1a1c1e]">#<?php echo $app['id']; ?> <?php echo htmlspecialchars($app['customer_name']); ?></span>
                <?php
                  $badgeBg = 'bg-amber-100 text-amber-800';
                  if ($app['status'] === 'Confirmed') $badgeBg = 'bg-blue-100 text-blue-800';
                  if ($app['status'] === 'Completed') $badgeBg = 'bg-emerald-100 text-emerald-800';
                  if ($app['status'] === 'Cancelled') $badgeBg = 'bg-red-100 text-red-800';
                ?>
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase <?php echo $badgeBg; ?>">
                  <?php echo htmlspecialchars($app['status']); ?>
                </span>
              </div>
              
              <div class="text-xs text-gray-600 font-medium">
                <p><i class="fa-solid fa-wrench mr-1 text-gray-400"></i> <?php echo htmlspecialchars($app['service_name'] ?? 'Servizio'); ?></p>
                <p><i class="fa-solid fa-car mr-1 text-gray-400"></i> <?php echo htmlspecialchars($app['vehicle_brand'] . ' ' . $app['vehicle_model']); ?> (<?php echo htmlspecialchars($app['vehicle_registration']); ?>)</p>
                <p><i class="fa-regular fa-calendar-check mr-1 text-gray-400"></i> <?php echo date('d/m/Y', strtotime($app['booking_date'])); ?> alle <?php echo date('H:i', strtotime($app['booking_time'])); ?></p>
              </div>

              <div class="mt-2 text-right">
                <a href="appointment-view.php?id=<?php echo $app['id']; ?>" class="text-[11px] font-extrabold text-[#d32f2f] hover:underline">
                  Dettagli &rarr;
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="mt-6 pt-4 border-t text-center">
      <a href="news-add.php" class="w-full inline-block bg-[#1a1c1e] hover:bg-[#d32f2f] text-white font-extrabold uppercase text-xs py-3 rounded transition-colors shadow">
        <i class="fa-solid fa-plus mr-1"></i> Nuova News Homepage
      </a>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'it',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek'
    },
    buttonText: {
      today:    'Oggi',
      month:    'Mese',
      week:     'Settimana'
    },
    events: <?php echo json_encode($calendarEvents); ?>,
    eventClick: function(info) {
      if (info.event.url) {
        window.location.href = info.event.url;
        info.jsEvent.preventDefault();
      }
    }
  });
  calendar.render();
});
</script>

<?php require_once __DIR__ . '/admin-footer.php'; ?>

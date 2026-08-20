<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';

$pageTitle = "Appuntamenti";

// Handle Status Change via POST
$actionMessage = '';
$actionError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $appointmentId = intval($_POST['appointment_id'] ?? 0);

    if ($action === 'update_status' && $appointmentId > 0) {
        $newStatus = $_POST['new_status'] ?? '';
        $validStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
        if (in_array($newStatus, $validStatuses)) {
            try {
                $db = getDBConnection();
                $stmt = $db->prepare("UPDATE appointments SET status = :status WHERE id = :id");
                $stmt->execute([':status' => $newStatus, ':id' => $appointmentId]);
                $actionMessage = "Stato dell'appuntamento #$appointmentId aggiornato a '$newStatus'.";
                sendBookingStatusUpdateEmail($appointmentId, $newStatus);
            } catch (Exception $e) {
                $actionError = "Errore durante l'aggiornamento: " . $e->getMessage();
            }
        }
    }
 elseif ($action === 'delete' && $appointmentId > 0) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM appointments WHERE id = :id");
            $stmt->execute([':id' => $appointmentId]);
            $actionMessage = "Appuntamento #$appointmentId eliminato con successo.";
        } catch (Exception $e) {
            $actionError = "Errore durante l'eliminazione: " . $e->getMessage();
        }
    }
}

// Include Header & Period Filter Logic
require_once __DIR__ . '/admin-header.php';

// Filters
$statusFilter = $_GET['status'] ?? 'all';
$dateFilter   = $_GET['date'] ?? '';

$appointments = [];
try {
    $db = getDBConnection();
    $sql = "
        SELECT a.*, s.name as service_name 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        WHERE 1=1
    ";
    $params = [];

    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $sql .= " AND a.status = :status";
        $params[':status'] = $statusFilter;
    }

    if (!empty($dateFilter)) {
        $sql .= " AND a.booking_date = :date";
        $params[':date'] = $dateFilter;
    } elseif (!empty($currentPeriod) && $currentPeriod !== 'all') {
        list($pStart, $pEnd) = getPeriodDateBounds($currentPeriod);
        $sql .= " AND a.booking_date BETWEEN :pstart AND :pend";
        $params[':pstart'] = $pStart;
        $params[':pend']   = $pEnd;
    }

    $sql .= " ORDER BY a.booking_date DESC, a.booking_time ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Fetch appointments error: " . $e->getMessage());
}
?>

<?php if (!empty($actionMessage)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'success',
        title: 'Operazione Completata',
        text: <?php echo json_encode($actionMessage); ?>,
        confirmButtonColor: '#d32f2f'
      });
    });
  </script>
<?php endif; ?>

<?php if (!empty($actionError)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'error',
        title: 'Errore',
        text: <?php echo json_encode($actionError); ?>,
        confirmButtonColor: '#d32f2f'
      });
    });
  </script>
<?php endif; ?>

<!-- Custom DataTables Styling Overrides for Theme Match -->
<style>
  .dataTables_wrapper .dataTables_length select {
    background-color: #F8F8F8;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 9999px;
    padding: 6px 14px;
    font-weight: 700;
    font-size: 12px;
    outline: none;
    color: #18181B;
  }
  .dataTables_wrapper .dataTables_filter input {
    background-color: #F8F8F8;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 9999px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 600;
    outline: none;
    margin-left: 8px;
    color: #18181B;
  }
  .dataTables_wrapper .dataTables_info {
    font-size: 12px;
    font-weight: 600;
    color: rgba(24, 24, 27, 0.5);
    padding-top: 20px;
  }
  .dataTables_wrapper .dataTables_paginate {
    padding-top: 16px;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 9999px !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    background: #FFFFFF !important;
    color: #18181B !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    padding: 6px 14px !important;
    margin: 0 3px !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #18181B !important;
    color: #FFFFFF !important;
    border-color: #18181B !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #18181B !important;
    color: #FFFFFF !important;
    border-color: #18181B !important;
    box-shadow: 0 4px 12px rgba(24, 24, 27, 0.15) !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    opacity: 0.4 !important;
    background: #F8F8F8 !important;
    color: #18181B !important;
    cursor: not-allowed !important;
  }
  table.dataTable.no-footer {
    border-bottom: 0 !important;
  }
</style>

<!-- Filters Toolbar -->
<div class="bg-white p-7 rounded-3xl shadow-sm border border-black/5 mb-6">
  <form method="GET" action="appointments.php" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
    
    <div>
      <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-1.5 font-display">Filtra per Stato</label>
      <select name="status" class="w-full px-4 py-2.5 bg-[#F8F8F8] border border-black/10 rounded-full text-xs font-bold outline-none focus:border-ink">
        <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>Tutti gli Stati</option>
        <option value="Pending" <?php echo ($statusFilter === 'Pending') ? 'selected' : ''; ?>>Pending (In Attesa)</option>
        <option value="Confirmed" <?php echo ($statusFilter === 'Confirmed') ? 'selected' : ''; ?>>Confirmed (Confermati)</option>
        <option value="Completed" <?php echo ($statusFilter === 'Completed') ? 'selected' : ''; ?>>Completed (Completati)</option>
        <option value="Cancelled" <?php echo ($statusFilter === 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Annullati)</option>
      </select>
    </div>

    <div>
      <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-1.5 font-display">Filtra per Data</label>
      <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" class="w-full px-4 py-2 bg-[#F8F8F8] border border-black/10 rounded-full text-xs font-bold outline-none focus:border-ink">
    </div>

    <div class="flex items-center gap-2">
      <button type="submit" class="bg-[#18181B] hover:bg-black text-white font-bold text-xs px-6 py-2.5 rounded-full transition-all shadow-sm">
        <i class="fa-solid fa-filter mr-1"></i> Filtra
      </button>
      <a href="appointments.php" class="bg-black/5 hover:bg-black/10 text-ink font-bold text-xs px-5 py-2.5 rounded-full transition-colors">
        Reset
      </a>
    </div>

  </form>
</div>

<!-- Appointments Table Card -->
<div class="bg-white rounded-3xl shadow-sm border border-black/5 overflow-hidden p-7">

  <div class="overflow-x-auto">
    <table id="appointmentsTable" class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="text-xs font-bold text-ink/40 uppercase border-b border-black/5 pb-3">
          <th class="pb-3 font-display">ID</th>
          <th class="pb-3 font-display">Cliente</th>
          <th class="pb-3 font-display">Telefono / Email</th>
          <th class="pb-3 font-display">Veicolo</th>
          <th class="pb-3 font-display">Servizio</th>
          <th class="pb-3 font-display">Data e Ora</th>
          <th class="pb-3 font-display">Stato</th>
          <th class="pb-3 font-display text-right">Azioni</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-black/5 font-medium text-ink">
        <?php foreach ($appointments as $app): ?>
          <tr class="hover:bg-black/[0.02] transition-colors">
            <td class="py-4 font-mono font-bold text-xs text-ink/70">#<?php echo $app['id']; ?></td>
            
            <td class="py-4">
              <span class="font-display font-bold text-sm text-ink block"><?php echo htmlspecialchars($app['customer_name']); ?></span>
            </td>

            <td class="py-4 text-xs">
              <p><i class="fa-solid fa-phone text-ink/40 mr-1"></i><?php echo htmlspecialchars($app['phone']); ?></p>
              <p class="text-ink/60"><i class="fa-regular fa-envelope text-ink/40 mr-1"></i><?php echo htmlspecialchars($app['email']); ?></p>
            </td>

            <td class="py-4">
              <span class="font-bold text-ink text-xs block"><?php echo htmlspecialchars($app['vehicle_brand'] . ' ' . $app['vehicle_model']); ?></span>
              <span class="text-[11px] bg-black/5 px-2 py-0.5 rounded-full font-mono font-bold text-ink/70"><?php echo htmlspecialchars($app['vehicle_registration']); ?></span>
            </td>

            <td class="py-4 font-semibold text-xs text-ink">
              <?php echo htmlspecialchars($app['service_name'] ?? 'N/D'); ?>
            </td>

            <td class="py-4 whitespace-nowrap text-xs">
              <p class="font-bold text-ink"><?php echo date('d/m/Y', strtotime($app['booking_date'])); ?></p>
              <p class="text-brand font-bold"><i class="fa-regular fa-clock mr-1"></i><?php echo date('H:i', strtotime($app['booking_time'])); ?></p>
            </td>

            <td class="py-4">
              <!-- Quick Status Change Form -->
              <form method="POST" action="appointments.php" class="inline-block">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                <?php
                  $selectBg = 'bg-amber-100 text-amber-800';
                  if ($app['status'] === 'Confirmed') $selectBg = 'bg-[#18181B] text-white';
                  if ($app['status'] === 'Completed') $selectBg = 'bg-emerald-100 text-emerald-800';
                  if ($app['status'] === 'Cancelled') $selectBg = 'bg-rose-100 text-rose-800';
                ?>
                <select name="new_status" onchange="this.form.submit()" class="text-xs font-bold px-3 py-1.5 rounded-full shadow-sm cursor-pointer border-0 outline-none <?php echo $selectBg; ?>">
                  <option value="Pending" <?php echo ($app['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Confirmed" <?php echo ($app['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                  <option value="Completed" <?php echo ($app['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="Cancelled" <?php echo ($app['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
              </form>
            </td>

            <td class="py-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-2">
                <a href="appointment-view.php?id=<?php echo $app['id']; ?>" class="bg-black/5 hover:bg-[#18181B] hover:text-white text-ink text-xs font-bold px-3 py-1.5 rounded-full transition-colors" title="Visualizza Scheda">
                  <i class="fa-solid fa-eye mr-1"></i> Scheda
                </a>

                <button type="button" onclick="confirmDelete(<?php echo $app['id']; ?>)" class="bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 text-xs font-bold px-3 py-1.5 rounded-full transition-colors" title="Elimina">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" action="appointments.php" class="hidden">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" id="delete_appointment_id" name="appointment_id" value="">
</form>

<script>
$(document).ready(function() {
  $('#appointmentsTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/it-IT.json'
    },
    order: [[0, 'desc']],
    pageLength: 15
  });
});

function confirmDelete(id) {
  Swal.fire({
    title: 'Sei sicuro?',
    text: `Stai per eliminare la prenotazione #${id}. L'azione non è reversibile!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d32f2f',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Sì, elimina!',
    cancelButtonText: 'Annulla'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('delete_appointment_id').value = id;
      document.getElementById('deleteForm').submit();
    }
  });
}
</script>

<?php require_once __DIR__ . '/admin-footer.php'; ?>

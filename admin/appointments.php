<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = "Gestione Appuntamenti";

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
            } catch (Exception $e) {
                $actionError = "Errore durante l'aggiornamento: " . $e->getMessage();
            }
        }
    } elseif ($action === 'delete' && $appointmentId > 0) {
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
    }

    $sql .= " ORDER BY a.booking_date DESC, a.booking_time ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Fetch appointments error: " . $e->getMessage());
}

require_once __DIR__ . '/admin-header.php';
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

<!-- Filters Toolbar -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
  <form method="GET" action="appointments.php" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
    
    <div>
      <label class="block text-xs font-extrabold text-gray-700 uppercase mb-1">Filtra per Stato</label>
      <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-[#d32f2f]">
        <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>Tutti gli Stati</option>
        <option value="Pending" <?php echo ($statusFilter === 'Pending') ? 'selected' : ''; ?>>Pending (In Attesa)</option>
        <option value="Confirmed" <?php echo ($statusFilter === 'Confirmed') ? 'selected' : ''; ?>>Confirmed (Confermati)</option>
        <option value="Completed" <?php echo ($statusFilter === 'Completed') ? 'selected' : ''; ?>>Completed (Completati)</option>
        <option value="Cancelled" <?php echo ($statusFilter === 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Annullati)</option>
      </select>
    </div>

    <div>
      <label class="block text-xs font-extrabold text-gray-700 uppercase mb-1">Filtra per Data</label>
      <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-[#d32f2f]">
    </div>

    <div class="flex items-center gap-2">
      <button type="submit" class="bg-[#1a1c1e] hover:bg-[#d32f2f] text-white font-extrabold text-xs uppercase px-5 py-2.5 rounded-lg transition-colors shadow">
        <i class="fa-solid fa-filter mr-1"></i> Filtra
      </button>
      <a href="appointments.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase px-4 py-2.5 rounded-lg transition-colors">
        Reset
      </a>
    </div>

  </form>
</div>

<!-- Appointments Table Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex justify-between items-center">
    <h3 class="font-black text-lg uppercase text-[#1a1c1e]">Elenco Prenotazioni (<?php echo count($appointments); ?>)</h3>
  </div>

  <div class="p-6 overflow-x-auto">
    <table id="appointmentsTable" class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="bg-gray-100 text-gray-700 font-extrabold uppercase text-xs">
          <th class="p-3 rounded-l">ID</th>
          <th class="p-3">Cliente</th>
          <th class="p-3">Telefono / Email</th>
          <th class="p-3">Veicolo</th>
          <th class="p-3">Servizio</th>
          <th class="p-3">Data e Ora</th>
          <th class="p-3">Stato</th>
          <th class="p-3 text-right rounded-r">Azioni</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 font-medium">
        <?php foreach ($appointments as $app): ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="p-3 font-extrabold text-[#1a1c1e]">#<?php echo $app['id']; ?></td>
            
            <td class="p-3">
              <span class="font-bold text-[#1a1c1e] block"><?php echo htmlspecialchars($app['customer_name']); ?></span>
            </td>

            <td class="p-3 text-xs">
              <p><i class="fa-solid fa-phone text-gray-400 mr-1"></i><?php echo htmlspecialchars($app['phone']); ?></p>
              <p class="text-gray-500"><i class="fa-regular fa-envelope text-gray-400 mr-1"></i><?php echo htmlspecialchars($app['email']); ?></p>
            </td>

            <td class="p-3">
              <span class="font-bold text-[#1a1c1e] block"><?php echo htmlspecialchars($app['vehicle_brand'] . ' ' . $app['vehicle_model']); ?></span>
              <span class="text-xs bg-gray-100 px-2 py-0.5 rounded font-mono font-bold text-gray-700"><?php echo htmlspecialchars($app['vehicle_registration']); ?></span>
            </td>

            <td class="p-3 font-semibold text-gray-800">
              <?php echo htmlspecialchars($app['service_name'] ?? 'N/D'); ?>
            </td>

            <td class="p-3 whitespace-nowrap text-xs">
              <p class="font-bold text-[#1a1c1e]"><?php echo date('d/m/Y', strtotime($app['booking_date'])); ?></p>
              <p class="text-[#d32f2f] font-extrabold"><i class="fa-regular fa-clock mr-1"></i><?php echo date('H:i', strtotime($app['booking_time'])); ?></p>
            </td>

            <td class="p-3">
              <!-- Quick Status Change Form -->
              <form method="POST" action="appointments.php" class="inline-block">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                <?php
                  $selectBg = 'bg-amber-100 text-amber-800 border-amber-300';
                  if ($app['status'] === 'Confirmed') $selectBg = 'bg-blue-100 text-blue-800 border-blue-300';
                  if ($app['status'] === 'Completed') $selectBg = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                  if ($app['status'] === 'Cancelled') $selectBg = 'bg-red-100 text-red-800 border-red-300';
                ?>
                <select name="new_status" onchange="this.form.submit()" class="text-xs font-extrabold px-2.5 py-1 rounded border shadow-sm cursor-pointer <?php echo $selectBg; ?>">
                  <option value="Pending" <?php echo ($app['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="Confirmed" <?php echo ($app['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                  <option value="Completed" <?php echo ($app['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="Cancelled" <?php echo ($app['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
              </form>
            </td>

            <td class="p-3 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-2">
                <a href="appointment-view.php?id=<?php echo $app['id']; ?>" class="bg-gray-100 hover:bg-[#1a1c1e] hover:text-white text-gray-800 text-xs font-extrabold px-3 py-1.5 rounded transition-colors" title="Visualizza Scheda">
                  <i class="fa-solid fa-eye"></i> Visualizza
                </a>

                <button type="button" onclick="confirmDelete(<?php echo $app['id']; ?>)" class="bg-red-50 hover:bg-red-600 hover:text-white text-[#d32f2f] text-xs font-extrabold px-3 py-1.5 rounded transition-colors" title="Elimina">
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

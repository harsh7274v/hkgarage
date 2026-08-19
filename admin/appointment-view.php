<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$appointmentId = intval($_GET['id'] ?? 0);
if ($appointmentId <= 0) {
    header("Location: appointments.php");
    exit;
}

$pageTitle = "Dettaglio Appuntamento #" . $appointmentId;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['status'] ?? '';
    $notes     = trim($_POST['notes'] ?? '');

    $validStatuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    if (in_array($newStatus, $validStatuses)) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("UPDATE appointments SET status = :status, notes = :notes WHERE id = :id");
            $stmt->execute([
                ':status' => $newStatus,
                ':notes'  => $notes,
                ':id'     => $appointmentId
            ]);
            $message = "Appuntamento aggiornato con successo!";
        } catch (Exception $e) {
            $error = "Errore durante l'aggiornamento: " . $e->getMessage();
        }
    }
}

// Fetch appointment details
$appointment = null;
try {
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT a.*, s.name as service_name, s.duration as service_duration, s.price as service_price 
        FROM appointments a 
        LEFT JOIN services s ON a.service_id = s.id 
        WHERE a.id = :id
    ");
    $stmt->execute([':id' => $appointmentId]);
    $appointment = $stmt->fetch();
} catch (Exception $e) {
    error_log("Fetch appointment view error: " . $e->getMessage());
}

if (!$appointment) {
    header("Location: appointments.php");
    exit;
}

require_once __DIR__ . '/admin-header.php';
?>

<div class="mb-6">
  <a href="appointments.php" class="text-xs font-bold text-gray-500 hover:text-[#d32f2f] uppercase transition-colors">
    &larr; Torna alla lista appuntamenti
  </a>
</div>

<?php if (!empty($message)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'success',
        title: 'Aggiornato',
        text: <?php echo json_encode($message); ?>,
        confirmButtonColor: '#d32f2f'
      });
    });
  </script>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
  
  <!-- Left Side: Appointment Information Cards -->
  <div class="lg:col-span-2 space-y-6">
    
    <!-- Header Summary Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-3">
          <h2 class="text-2xl font-black text-[#1a1c1e]">Appuntamento #<?php echo $appointment['id']; ?></h2>
          <?php
            $badgeBg = 'bg-amber-100 text-amber-800';
            if ($appointment['status'] === 'Confirmed') $badgeBg = 'bg-blue-100 text-blue-800';
            if ($appointment['status'] === 'Completed') $badgeBg = 'bg-emerald-100 text-emerald-800';
            if ($appointment['status'] === 'Cancelled') $badgeBg = 'bg-red-100 text-red-800';
          ?>
          <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase <?php echo $badgeBg; ?>">
            <?php echo htmlspecialchars($appointment['status']); ?>
          </span>
        </div>
        <p class="text-xs text-gray-500 font-semibold mt-1">
          Inviato il <?php echo date('d/m/Y H:i', strtotime($appointment['created_at'])); ?>
        </p>
      </div>

      <div class="text-right">
        <div class="text-lg font-black text-[#d32f2f]">
          <i class="fa-regular fa-calendar-check mr-1"></i>
          <?php echo date('d/m/Y', strtotime($appointment['booking_date'])); ?>
        </div>
        <div class="text-sm font-extrabold text-gray-700">
          Ore <?php echo date('H:i', strtotime($appointment['booking_time'])); ?>
        </div>
      </div>
    </div>

    <!-- Customer & Vehicle Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Customer Card -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-xs font-extrabold uppercase text-gray-400 tracking-wider mb-4 border-b pb-2">
          <i class="fa-solid fa-user text-[#d32f2f] mr-1"></i> Dettagli Cliente
        </h3>
        <div class="space-y-3 text-sm">
          <div>
            <span class="text-xs text-gray-400 font-bold uppercase block">Nome e Cognome</span>
            <span class="font-extrabold text-[#1a1c1e] text-base"><?php echo htmlspecialchars($appointment['customer_name']); ?></span>
          </div>

          <div>
            <span class="text-xs text-gray-400 font-bold uppercase block">Telefono / Cellulare</span>
            <a href="tel:<?php echo htmlspecialchars($appointment['phone']); ?>" class="font-bold text-blue-600 hover:underline">
              <i class="fa-solid fa-phone text-xs mr-1"></i> <?php echo htmlspecialchars($appointment['phone']); ?>
            </a>
          </div>

          <div>
            <span class="text-xs text-gray-400 font-bold uppercase block">Indirizzo Email</span>
            <a href="mailto:<?php echo htmlspecialchars($appointment['email']); ?>" class="font-bold text-blue-600 hover:underline">
              <i class="fa-regular fa-envelope text-xs mr-1"></i> <?php echo htmlspecialchars($appointment['email']); ?>
            </a>
          </div>
        </div>
      </div>

      <!-- Vehicle Card -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-xs font-extrabold uppercase text-gray-400 tracking-wider mb-4 border-b pb-2">
          <i class="fa-solid fa-car text-[#d32f2f] mr-1"></i> Dettagli Veicolo
        </h3>
        <div class="space-y-3 text-sm">
          <div>
            <span class="text-xs text-gray-400 font-bold uppercase block">Marca & Modello</span>
            <span class="font-extrabold text-[#1a1c1e] text-base">
              <?php echo htmlspecialchars($appointment['vehicle_brand'] . ' ' . $appointment['vehicle_model']); ?>
            </span>
          </div>

          <div>
            <span class="text-xs text-gray-400 font-bold uppercase block">Targa Veicolo</span>
            <span class="inline-block bg-gray-100 border border-gray-300 font-mono font-black text-sm px-3 py-1 rounded text-gray-800 tracking-wider">
              <?php echo htmlspecialchars($appointment['vehicle_registration']); ?>
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Service Requested Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
      <h3 class="text-xs font-extrabold uppercase text-gray-400 tracking-wider mb-4 border-b pb-2">
        <i class="fa-solid fa-wrench text-[#d32f2f] mr-1"></i> Servizio Richiesto
      </h3>
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-lg font-black text-[#1a1c1e]"><?php echo htmlspecialchars($appointment['service_name'] ?? 'Servizio Meccanico'); ?></h4>
          <p class="text-xs text-gray-500 font-semibold mt-1">Durata stimata: <?php echo intval($appointment['service_duration'] ?? 30); ?> minuti</p>
        </div>
        <div class="text-right">
          <span class="text-xl font-black text-[#d32f2f]">€<?php echo number_format($appointment['service_price'] ?? 0, 2); ?></span>
        </div>
      </div>
    </div>

  </div>

  <!-- Right Side: Status Management & Admin Notes Form -->
  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 h-fit">
    <h3 class="text-xs font-extrabold uppercase text-gray-400 tracking-wider mb-4 border-b pb-2">
      <i class="fa-solid fa-sliders text-[#d32f2f] mr-1"></i> Gestione Stato & Note
    </h3>

    <form method="POST" action="appointment-view.php?id=<?php echo $appointment['id']; ?>" class="space-y-6">
      
      <div>
        <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Stato Appuntamento</label>
        <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg font-extrabold text-sm focus:ring-2 focus:ring-[#d32f2f]">
          <option value="Pending" <?php echo ($appointment['status'] === 'Pending') ? 'selected' : ''; ?>>Pending (In attesa)</option>
          <option value="Confirmed" <?php echo ($appointment['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed (Confermato)</option>
          <option value="Completed" <?php echo ($appointment['status'] === 'Completed') ? 'selected' : ''; ?>>Completed (Completato)</option>
          <option value="Cancelled" <?php echo ($appointment['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Annullato)</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Note Cliente & Interna</label>
        <textarea name="notes" rows="6" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-[#d32f2f]" placeholder="Aggiungi dettagli o note sull'intervento..."><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></textarea>
      </div>

      <button type="submit" class="w-full bg-[#d32f2f] hover:bg-[#b71c1c] text-white font-extrabold uppercase py-3.5 rounded-lg shadow-lg transition-transform hover:-translate-y-0.5 text-sm flex items-center justify-center gap-2">
        <i class="fa-solid fa-floppy-disk"></i> Salva Modifiche
      </button>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>

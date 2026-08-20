<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';

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
            sendBookingStatusUpdateEmail($appointmentId, $newStatus);
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

<!-- Back Link Button -->
<div class="mb-6">
  <a href="appointments.php" class="inline-flex items-center gap-2 bg-white px-5 py-2.5 rounded-full border border-black/5 shadow-sm text-xs font-bold text-ink/70 hover:text-ink hover:bg-black/5 transition-all">
    <i class="fa-solid fa-arrow-left text-xs"></i>
    <span>Torna alla lista appuntamenti</span>
  </a>
</div>

<?php if (!empty($message)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'success',
        title: 'Operazione Completata',
        text: <?php echo json_encode($message); ?>,
        confirmButtonColor: '#18181B'
      });
    });
  </script>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  
  <!-- Left Side: Appointment Information Cards -->
  <div class="lg:col-span-2 space-y-6">
    
    <!-- Header Summary Card -->
    <div class="bg-white p-7 rounded-3xl shadow-sm border border-black/5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
      <div>
        <div class="flex items-center gap-3">
          <h2 class="font-display font-black text-2xl text-[#18181B]">Appuntamento #<?php echo $appointment['id']; ?></h2>
          <?php
            $badgeBg = 'bg-amber-100 text-amber-800';
            if ($appointment['status'] === 'Confirmed') $badgeBg = 'bg-[#18181B] text-white';
            if ($appointment['status'] === 'Completed') $badgeBg = 'bg-emerald-100 text-emerald-800';
            if ($appointment['status'] === 'Cancelled') $badgeBg = 'bg-rose-100 text-rose-800';
          ?>
          <span class="px-4 py-1.5 rounded-full text-xs font-bold shadow-sm <?php echo $badgeBg; ?>">
            <?php echo htmlspecialchars($appointment['status']); ?>
          </span>
        </div>
        <p class="text-xs text-ink/50 font-medium mt-1.5">
          Inviato il <?php echo date('d/m/Y H:i', strtotime($appointment['created_at'])); ?>
        </p>
      </div>

      <div class="bg-[#F8F8F8] px-5 py-3 rounded-2xl border border-black/5 text-right flex-shrink-0">
        <div class="text-base font-display font-black text-[#18181B]">
          <i class="fa-regular fa-calendar-check text-brand mr-1.5"></i>
          <?php echo date('d/m/Y', strtotime($appointment['booking_date'])); ?>
        </div>
        <div class="text-xs font-bold text-brand mt-0.5">
          <i class="fa-regular fa-clock mr-1"></i>Ore <?php echo date('H:i', strtotime($appointment['booking_time'])); ?>
        </div>
      </div>
    </div>

    <!-- Customer & Vehicle Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Customer Card -->
      <div class="bg-white p-7 rounded-3xl shadow-sm border border-black/5 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-3 mb-4 pb-3 border-b border-black/5">
            <div class="w-9 h-9 rounded-2xl bg-black/5 flex items-center justify-center text-[#18181B]">
              <i class="fa-solid fa-user text-xs"></i>
            </div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-ink/40 font-display">Dettagli Cliente</h3>
          </div>
          <div class="space-y-4">
            <div>
              <span class="text-[11px] text-ink/40 font-bold uppercase block tracking-wider mb-0.5">Nome e Cognome</span>
              <span class="font-display font-black text-[#18181B] text-base"><?php echo htmlspecialchars($appointment['customer_name']); ?></span>
            </div>

            <div>
              <span class="text-[11px] text-ink/40 font-bold uppercase block tracking-wider mb-1">Telefono / Cellulare</span>
              <a href="tel:<?php echo htmlspecialchars($appointment['phone']); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#F8F8F8] border border-black/5 text-xs font-bold text-ink hover:bg-[#18181B] hover:text-white transition-colors">
                <i class="fa-solid fa-phone text-xs"></i>
                <span><?php echo htmlspecialchars($appointment['phone']); ?></span>
              </a>
            </div>

            <div>
              <span class="text-[11px] text-ink/40 font-bold uppercase block tracking-wider mb-1">Indirizzo Email</span>
              <a href="mailto:<?php echo htmlspecialchars($appointment['email']); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#F8F8F8] border border-black/5 text-xs font-bold text-ink hover:bg-[#18181B] hover:text-white transition-colors">
                <i class="fa-regular fa-envelope text-xs"></i>
                <span><?php echo htmlspecialchars($appointment['email']); ?></span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehicle Card -->
      <div class="bg-white p-7 rounded-3xl shadow-sm border border-black/5 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-3 mb-4 pb-3 border-b border-black/5">
            <div class="w-9 h-9 rounded-2xl bg-black/5 flex items-center justify-center text-[#18181B]">
              <i class="fa-solid fa-car text-xs"></i>
            </div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-ink/40 font-display">Dettagli Veicolo</h3>
          </div>
          <div class="space-y-4">
            <div>
              <span class="text-[11px] text-ink/40 font-bold uppercase block tracking-wider mb-0.5">Marca & Modello</span>
              <span class="font-display font-black text-[#18181B] text-base">
                <?php echo htmlspecialchars($appointment['vehicle_brand'] . ' ' . $appointment['vehicle_model']); ?>
              </span>
            </div>

            <div>
              <span class="text-[11px] text-ink/40 font-bold uppercase block tracking-wider mb-1">Targa Veicolo</span>
              <span class="inline-block bg-[#18181B] text-white font-mono font-bold text-xs px-4 py-1.5 rounded-full shadow-sm tracking-wider">
                <?php echo htmlspecialchars($appointment['vehicle_registration']); ?>
              </span>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Service Requested Card -->
    <div class="bg-white p-7 rounded-3xl shadow-sm border border-black/5">
      <div class="flex items-center gap-3 mb-4 pb-3 border-b border-black/5">
        <div class="w-9 h-9 rounded-2xl bg-black/5 flex items-center justify-center text-[#18181B]">
          <i class="fa-solid fa-wrench text-xs"></i>
        </div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-ink/40 font-display">Servizio Richiesto</h3>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h4 class="font-display font-black text-xl text-[#18181B]"><?php echo htmlspecialchars($appointment['service_name'] ?? 'Servizio Meccanico'); ?></h4>
          <p class="text-xs text-ink/50 font-semibold mt-1">Durata stimata: <?php echo intval($appointment['service_duration'] ?? 30); ?> minuti</p>
        </div>
        <div class="text-right">
          <span class="font-display font-black text-2xl text-[#18181B]">€<?php echo number_format($appointment['service_price'] ?? 0, 2); ?></span>
        </div>
      </div>
    </div>

  </div>

  <!-- Right Side: Status Management & Admin Notes Form -->
  <div class="bg-white p-7 rounded-3xl shadow-sm border border-black/5 h-fit">
    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-black/5">
      <div class="w-9 h-9 rounded-2xl bg-black/5 flex items-center justify-center text-[#18181B]">
        <i class="fa-solid fa-sliders text-xs"></i>
      </div>
      <h3 class="text-xs font-bold uppercase tracking-wider text-ink/40 font-display">Gestione Stato & Note</h3>
    </div>

    <form method="POST" action="appointment-view.php?id=<?php echo $appointment['id']; ?>" class="space-y-5">
      
      <div>
        <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-2 font-display">Stato Appuntamento</label>
        <select name="status" class="w-full px-4 py-3 bg-[#F8F8F8] border border-black/10 rounded-2xl font-bold text-xs outline-none focus:border-[#18181B] cursor-pointer">
          <option value="Pending" <?php echo ($appointment['status'] === 'Pending') ? 'selected' : ''; ?>>Pending (In attesa)</option>
          <option value="Confirmed" <?php echo ($appointment['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed (Confermato)</option>
          <option value="Completed" <?php echo ($appointment['status'] === 'Completed') ? 'selected' : ''; ?>>Completed (Completato)</option>
          <option value="Cancelled" <?php echo ($appointment['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled (Annullato)</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-ink/60 uppercase tracking-wider mb-2 font-display">Note Cliente & Interna</label>
        <textarea name="notes" rows="6" class="w-full p-4 bg-[#F8F8F8] border border-black/10 rounded-2xl text-xs font-medium outline-none focus:border-[#18181B]" placeholder="Aggiungi dettagli o note sull'intervento..."><?php echo htmlspecialchars($appointment['notes'] ?? ''); ?></textarea>
      </div>

      <button type="submit" class="w-full bg-[#18181B] hover:bg-black text-white font-bold py-4 rounded-2xl shadow-md transition-all hover:scale-[1.01] active:scale-95 text-xs flex items-center justify-center gap-2">
        <i class="fa-solid fa-floppy-disk text-xs"></i>
        <span>Salva Modifiche</span>
      </button>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>

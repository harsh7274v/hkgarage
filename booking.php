<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$pageTitle = "Prenota Appuntamento | HK Garage";

// Fetch active services for dropdown
$services = [];
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT id, name, description, duration, price FROM services WHERE active = 1 ORDER BY id ASC");
    $services = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Booking page services fetch error: " . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header Hero -->
<div class="pt-32 pb-12 bg-cream border-b border-ink/5">
  <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
    <div class="inline-flex items-center gap-2 mb-3 justify-center">
      <span class="w-8 h-1 bg-brand rounded-full"></span>
      <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">PRENOTAZIONE ONLINE</span>
      <span class="w-8 h-1 bg-brand rounded-full"></span>
    </div>
    <h1 class="font-display font-black text-ink text-4xl lg:text-5xl tracking-tight mb-3">
      Fissa il tuo appuntamento in officina
    </h1>
    <p class="text-ink/60 font-medium max-w-xl mx-auto text-base">
      Seleziona il servizio desiderato, la data e l'orario disponibile per il tuo intervento a Costa di Mezzate.
    </p>
  </div>
</div>

<main class="py-16 bg-[#FDFCFA]">
  <div class="max-w-3xl mx-auto px-5">
    
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-ink/10">
      
      <!-- Top banner accent -->
      <div class="bg-ink text-white p-6 sm:p-8">
        <div class="flex items-center gap-2 mb-2">
          <i class="fa-solid fa-calendar-check text-brand text-base"></i>
          <span class="text-xs font-black text-brand tracking-[0.2em] uppercase font-display">SCHEDULING APPOINTMENT</span>
        </div>
        <h2 class="font-display font-black text-2xl sm:text-3xl text-white">Modulo di Prenotazione</h2>
        <p class="text-xs sm:text-sm text-white/60 mt-1 font-medium">
          Tutti i campi contrassegnati con * sono obbligatori per confermare lo slot.
        </p>
      </div>

      <div class="p-6 sm:p-10">
        <form id="bookingForm" class="space-y-8">
          
          <!-- Step 1: Vehicle & Customer Info -->
          <div>
            <h3 class="font-display font-black text-xl text-ink mb-4 flex items-center gap-3 border-b border-ink/10 pb-3">
              <span class="w-8 h-8 rounded-full bg-brand/10 text-brand text-sm flex items-center justify-center font-black">1</span>
              Dati Cliente e Veicolo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Nome e Cognome *</label>
                <input type="text" id="customer_name" name="customer_name" required placeholder="Es. Mario Rossi" class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Telefono / Cellulare *</label>
                <input type="tel" id="phone" name="phone" required placeholder="Es. 339 1234567" class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all">
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Indirizzo Email *</label>
                <input type="email" id="email" name="email" required placeholder="Es. mario.rossi@email.it" class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Marca Veicolo *</label>
                <input type="text" id="vehicle_brand" name="vehicle_brand" required placeholder="Es. Volkswagen" class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Modello Veicolo *</label>
                <input type="text" id="vehicle_model" name="vehicle_model" required placeholder="Es. Golf VII 1.6 TDI" class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all">
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Targa Veicolo *</label>
                <input type="text" id="vehicle_registration" name="vehicle_registration" required placeholder="Es. AB123CD" class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-bold uppercase tracking-wider transition-all">
              </div>
            </div>
          </div>

          <!-- Step 2: Select Service -->
          <div>
            <h3 class="font-display font-black text-xl text-ink mb-4 flex items-center gap-3 border-b border-ink/10 pb-3">
              <span class="w-8 h-8 rounded-full bg-brand/10 text-brand text-sm flex items-center justify-center font-black">2</span>
              Seleziona Servizio
            </h3>

            <div>
              <label class="block text-xs font-bold text-ink uppercase mb-1.5">Servizio Richiesto *</label>
              <select id="service_id" name="service_id" required class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-semibold transition-all">
                <option value="">-- Seleziona un servizio dall'elenco --</option>
                <?php foreach ($services as $srv): ?>
                  <option value="<?php echo $srv['id']; ?>" data-duration="<?php echo $srv['duration']; ?>">
                    <?php echo htmlspecialchars($srv['name']); ?> (Stima: <?php echo $srv['duration']; ?> min)
                  </option>
                <?php endforeach; ?>
                <option value="other">Altro (Specificare)</option>
              </select>
            </div>

            <!-- Conditional Custom Service Field for 'Altro' -->
            <div id="otherServiceGroupBooking" class="mt-4 hidden">
              <label class="block text-xs font-bold text-brand uppercase mb-1.5"><i class="fa-solid fa-pen-to-square mr-1"></i> Specificare il servizio richiesto *</label>
              <input type="text" id="custom_service" name="custom_service" placeholder="Es. Sostituzione tergicristalli, diagnosi rumore, lucidatura fari..." class="w-full h-12 px-4 bg-brand/5 border border-brand/30 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all">
            </div>
          </div>

          <!-- Step 3: Date & Slot Selection -->
          <div>
            <h3 class="font-display font-black text-xl text-ink mb-4 flex items-center gap-3 border-b border-ink/10 pb-3">
              <span class="w-8 h-8 rounded-full bg-brand/10 text-brand text-sm flex items-center justify-center font-black">3</span>
              Seleziona Data e Orario
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Data Appuntamento *</label>
                <div class="relative">
                  <input type="text" id="booking_date" name="booking_date" required placeholder="Clicca per scegliere la data" readonly class="w-full h-12 px-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-semibold cursor-pointer">
                  <i class="fa-regular fa-calendar-days absolute right-4 top-3.5 text-ink/40 pointer-events-none"></i>
                </div>
                <p class="text-xs text-ink/60 mt-1.5 font-medium"><i class="fa-solid fa-circle-info text-brand mr-1"></i>Orari: Lun-Ven 08:00-18:30 | Sab 08:00-12:00</p>
              </div>

              <div>
                <label class="block text-xs font-bold text-ink uppercase mb-1.5">Fasce Orarie Disponibili *</label>
                <input type="hidden" id="booking_time" name="booking_time" required>
                
                <div id="slotsContainer" class="min-h-[140px] bg-cream/30 p-4 rounded-xl border border-ink/10 flex items-center justify-center">
                  <p class="text-xs text-ink/50 font-semibold text-center">
                    Seleziona prima una data per caricare gli orari disponibili.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 4: Notes & Confirm -->
          <div>
            <h3 class="font-display font-black text-xl text-ink mb-4 flex items-center gap-3 border-b border-ink/10 pb-3">
              <span class="w-8 h-8 rounded-full bg-brand/10 text-brand text-sm flex items-center justify-center font-black">4</span>
              Note Aggiuntive e Conferma
            </h3>

            <div class="mb-6">
              <label class="block text-xs font-bold text-ink uppercase mb-1.5">Note o Sintomi del Veicolo (Opzionale)</label>
              <textarea id="notes" name="notes" rows="3" placeholder="Descrivi eventuali rumori, avvarie o richieste particolari..." class="w-full p-4 bg-cream/50 border border-ink/15 rounded-xl focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm font-medium transition-all resize-none"></textarea>
            </div>

            <button type="submit" id="submitBtn" class="w-full h-14 bg-brand hover:bg-brand-dark text-white font-bold uppercase text-base rounded-full shadow-lg shadow-brand/20 transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
              <i class="fa-solid fa-circle-check"></i> Conferma e Invia Prenotazione
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const bookingDateInput = document.getElementById('booking_date');
  const bookingTimeInput = document.getElementById('booking_time');
  const slotsContainer = document.getElementById('slotsContainer');
  const bookingForm = document.getElementById('bookingForm');
  const submitBtn = document.getElementById('submitBtn');

  // Initialize Flatpickr
  const fp = flatpickr(bookingDateInput, {
    locale: "it",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F Y (l)",
    minDate: "today",
    disable: [
      function(date) {
        return (date.getDay() === 0);
      }
    ],
    onChange: function(selectedDates, dateStr) {
      if (dateStr) {
        loadTimeSlots(dateStr);
      }
    }
  });

  function renderSlotsGrid(data) {
    if (!data.success) {
      slotsContainer.innerHTML = `<p class="text-xs text-brand font-bold">${data.message}</p>`;
      return;
    }

    if (data.is_closed) {
      slotsContainer.innerHTML = '<p class="text-xs text-brand font-bold uppercase"><i class="fa-solid fa-store-slash mr-1"></i> Officina chiusa di domenica.</p>';
      return;
    }

    if (!data.slots || data.slots.length === 0) {
      slotsContainer.innerHTML = '<p class="text-xs text-ink/60 font-bold">Nessun orario disponibile per la data selezionata.</p>';
      return;
    }

    let html = '<div class="grid grid-cols-3 sm:grid-cols-4 gap-2 w-full max-h-56 overflow-y-auto p-1">';
    data.slots.forEach(slot => {
      if (slot.available) {
        html += `
          <button type="button" data-time="${slot.time}" class="slot-btn border-2 border-ink/10 hover:border-brand hover:bg-brand/5 text-ink font-bold py-2.5 px-2 rounded-xl text-xs transition-all flex flex-col items-center justify-center">
            <span>${slot.time}</span>
            <span class="text-[10px] text-emerald-600 font-bold">Libero</span>
          </button>
        `;
      } else {
        html += `
          <button type="button" disabled class="border border-ink/5 bg-cream/60 text-ink/30 font-bold py-2.5 px-2 rounded-xl text-xs cursor-not-allowed flex flex-col items-center justify-center">
            <span class="line-through">${slot.time}</span>
            <span class="text-[10px] text-brand/60 font-normal">Occupato</span>
          </button>
        `;
      }
    });
    html += '</div>';
    slotsContainer.innerHTML = html;

    // Slot click events
    document.querySelectorAll('.slot-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.slot-btn').forEach(b => {
          b.classList.remove('bg-brand', 'text-white', 'border-brand', 'shadow-md');
          b.classList.add('border-ink/10', 'text-ink');
        });
        this.classList.remove('border-ink/10', 'text-ink');
        this.classList.add('bg-brand', 'text-white', 'border-brand', 'shadow-md');
        bookingTimeInput.value = this.dataset.time;
      });
    });
  }

  // Fetch available slots with 5-minute cookie cache
  function loadTimeSlots(dateStr) {
    slotsContainer.innerHTML = '<div class="text-center text-ink/60 py-4 text-xs font-bold"><i class="fa-solid fa-spinner fa-spin mr-2 text-brand"></i>Caricamento orari disponibili...</div>';
    bookingTimeInput.value = '';

    // 1. Check 5-minute cookie cache
    if (typeof getSlotsCacheCookie === 'function') {
      const cachedData = getSlotsCacheCookie(dateStr);
      if (cachedData) {
        console.log(`[Cache Hit - 5 Min Cookie] Loaded slot availability for ${dateStr} from cookie cache.`);
        renderSlotsGrid(cachedData);
        return;
      }
    }

    // 2. Cache miss or expired (> 5 min) -> Fetch fresh from API
    console.log(`[Cache Miss / Expired] Fetching fresh availability for ${dateStr} from API...`);
    fetch(`api/get-slots.php?date=${dateStr}`)
      .then(res => res.json())
      .then(data => {
        if (data && data.success && !data.is_closed && typeof setSlotsCacheCookie === 'function') {
          setSlotsCacheCookie(dateStr, data);
        }
        renderSlotsGrid(data);
      })
      .catch(err => {
        console.error('Error fetching slots:', err);
        slotsContainer.innerHTML = '<p class="text-xs text-brand font-bold">Errore caricamento orari.</p>';
      });
  // Toggle Custom Service Input Field for 'Altro'
  const serviceSelectInput = document.getElementById('service_id');
  const otherServiceGroupBooking = document.getElementById('otherServiceGroupBooking');
  const customServiceInput = document.getElementById('custom_service');

  if (serviceSelectInput) {
    serviceSelectInput.addEventListener('change', function() {
      if (this.value === 'other') {
        if (otherServiceGroupBooking) otherServiceGroupBooking.classList.remove('hidden');
        if (customServiceInput) customServiceInput.required = true;
      } else {
        if (otherServiceGroupBooking) otherServiceGroupBooking.classList.add('hidden');
        if (customServiceInput) {
          customServiceInput.required = false;
          customServiceInput.value = '';
        }
      }
    });
  }

  // Handle Form Submission (INSTANT NON-BLOCKING OPTIMISTIC CONFIRMATION)
  bookingForm.addEventListener('submit', function(e) {
    e.preventDefault();

    if (!bookingTimeInput.value) {
      Swal.fire({
        icon: 'warning',
        title: 'Orario non selezionato',
        text: 'Seleziona una fascia oraria disponibile prima di inviare.',
        confirmButtonColor: '#E63946'
      });
      return;
    }

    const formData = new FormData(bookingForm);
    const bookedDate = formData.get('booking_date');
    const serviceSelectElem = document.getElementById('service_id');
    const customServiceVal = formData.get('custom_service');

    let selectedServiceName = '';
    if (serviceSelectElem && serviceSelectElem.value === 'other' && customServiceVal) {
      selectedServiceName = 'Altro: ' + customServiceVal;
    } else if (serviceSelectElem && serviceSelectElem.selectedIndex >= 0) {
      selectedServiceName = serviceSelectElem.options[serviceSelectElem.selectedIndex].text;
    }

    const customerDetails = {
      customerName: formData.get('customer_name'),
      customerEmail: formData.get('email'),
      serviceName: selectedServiceName,
      date: formData.get('booking_date'),
      time: formData.get('booking_time'),
      vehicle: (formData.get('vehicle_brand') || '') + ' ' + (formData.get('vehicle_model') || '') + ' (' + (formData.get('vehicle_registration') || '').toUpperCase() + ')'
    };

    // 1. INSTANTLY show admin-themed thank you modal (Zero latency!)
    showThankYouModal(customerDetails);

    // 2. Perform backend DB save & PHPMailer emails asynchronously in the background
    fetch('api/book-appointment.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data && data.success) {
        clearSlotsCacheCookie(bookedDate);
      }
    })
    .catch(err => {
      console.error('Background booking submission error:', err);
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

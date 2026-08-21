<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = "Contattaci | HK Garage";

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Header -->
<div class="pt-32 pb-12 bg-cream border-b border-ink/5">
  <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
    <div class="inline-flex items-center gap-2 mb-3 justify-center">
      <span class="w-8 h-1 bg-brand rounded-full"></span>
      <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">CONTATTI & INFORMAZIONI</span>
      <span class="w-8 h-1 bg-brand rounded-full"></span>
    </div>
    <h1 class="font-display font-black text-ink text-4xl lg:text-5xl tracking-tight mb-3">
      Mettiti in contatto con noi
    </h1>
    <p class="text-ink/60 font-medium max-w-xl mx-auto text-base">
      Siamo a tua disposizione per informazioni, preventivi gratuiti o assistenza meccanica personalizzata.
    </p>
  </div>
</div>

<main class="py-16 bg-[#FDFCFA]">
  <div class="max-w-7xl mx-auto px-5 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8">
    
    <!-- Contact Info Card -->
    <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-ink/10 flex flex-col justify-between">
      <div>
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="w-8 h-1 bg-brand rounded-full"></span>
          <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">HK GARAGE</span>
        </div>
        <h2 class="font-display font-black text-2xl sm:text-3xl text-ink mb-8">Informazioni Officina</h2>
        
        <div class="space-y-6">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-brand/10 text-brand rounded-2xl flex items-center justify-center flex-shrink-0 text-xl font-bold">
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
              <h3 class="text-xs uppercase text-ink/40 font-black tracking-wider font-display mb-1">DOVE SIAMO</h3>
              <p class="font-bold text-ink text-lg font-display">HK Garage</p>
              <p class="text-ink/70 text-sm leading-relaxed">via dei Livelli di Sopra, 3A<br>24060 Villa Landri (BG)</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-brand/10 text-brand rounded-2xl flex items-center justify-center flex-shrink-0 text-xl font-bold">
              <i class="fa-solid fa-phone"></i>
            </div>
            <div>
              <h3 class="text-xs uppercase text-ink/40 font-black tracking-wider font-display mb-1">TELEFONO & WHATSAPP</h3>
              <a href="tel:+393202819584" class="font-bold text-ink hover:text-brand text-lg font-display transition-colors">320 281 9584</a>
              <p class="text-ink/60 text-sm">Assistenza diretta: Harshit & Karan</p>
            </div>
          </div>

          <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-brand/10 text-brand rounded-2xl flex items-center justify-center flex-shrink-0 text-xl font-bold">
              <i class="fa-solid fa-clock"></i>
            </div>
            <div>
              <h3 class="text-xs uppercase text-ink/40 font-black tracking-wider font-display mb-1">ORARI APERTURA</h3>
              <p class="font-bold text-ink text-sm">Lunedì – Venerdì: 08:00 – 12:30 / 14:00 – 19:00</p>
              <p class="font-bold text-ink text-sm">Sabato: 08:00 – 12:00 <span class="text-ink/60 font-medium text-xs">(oppure su appuntamento)</span></p>
              <p class="text-brand font-bold text-sm">Domenica: Chiuso</p>
            </div>
          </div>
        </div>
      </div>

      <div class="pt-8 mt-8 border-t border-ink/10 flex items-center gap-3">
        <a href="https://wa.me/393202819584" target="_blank" rel="noopener" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl text-center transition-colors flex items-center justify-center gap-2">
          <i class="fa-brands fa-whatsapp text-base"></i> Scrivici su WhatsApp
        </a>
      </div>
    </div>

    <!-- Booking Call-to-Action Card -->
    <div class="bg-ink p-8 sm:p-10 rounded-3xl shadow-xl text-white border border-ink/20 flex flex-col justify-between relative overflow-hidden">
      <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-brand/20 blur-3xl pointer-events-none"></div>

      <div>
        <div class="inline-flex items-center gap-2 mb-4">
          <span class="w-8 h-1 bg-brand rounded-full"></span>
          <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">PRENOTAZIONE RAPIDA</span>
        </div>
        <h2 class="font-display font-black text-3xl text-white mb-4">Vuoi fissare un appuntamento?</h2>
        <p class="text-white/70 font-normal leading-relaxed mb-6">
          Il nostro sistema di prenotazione online ti permette di scegliere la data e l'orario perfetto per il tuo intervento in soli 2 minuti.
        </p>
      </div>

      <div class="bg-white/5 p-6 rounded-2xl border border-white/10 text-center mb-8">
        <i class="fa-solid fa-calendar-check text-4xl text-brand mb-3"></i>
        <h3 class="font-display font-bold text-lg text-white">Disponibilità Orari in Tempo Reale</h3>
        <p class="text-xs text-white/60 mt-1">Verifica istantaneamente gli slot liberi per la tua vettura.</p>
      </div>

      <button onclick="openBookingModal()" class="w-full h-14 bg-brand hover:bg-brand-dark text-white font-bold text-base rounded-full shadow-lg transition-all hover:scale-105 flex items-center justify-center gap-2">
        <i class="fa-solid fa-calendar-plus text-sm"></i>
        Prenota Ora Online
      </button>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$pageTitle = "Officina Meccanica & Diagnosi Elettronica Costa di Mezzate";

// Fetch 3 latest news from Database
$latestNews = [];
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM news ORDER BY published_date DESC, id DESC LIMIT 3");
    $latestNews = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Homepage news fetch error: " . $e->getMessage());
}

if (empty($latestNews)) {
    $latestNews = [
        [
            'id' => 1,
            'title' => 'Nuova macchina di diagnosi Bosch KTS 990',
            'description' => 'Abbiamo investito nella nuova diagnostica Bosch per garantire interventi ancora più rapidi e precisi su tutte le marche europee.',
            'published_date' => '2026-07-10',
            'image' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?crop=entropy&cs=srgb&fm=jpg&w=800&q=80'
        ],
        [
            'id' => 2,
            'title' => 'Cambio gomme estivo: sconto 15% fino a fine mese',
            'description' => 'Prenota il cambio stagionale entro luglio e ricevi uno sconto del 15% su montaggio ed equilibratura. Custodia gratuita.',
            'published_date' => '2026-07-02',
            'image' => 'https://images.pexels.com/photos/8478259/pexels-photo-8478259.jpeg?auto=compress&cs=tinysrgb&w=800'
        ],
        [
            'id' => 3,
            'title' => '5 controlli da fare prima delle vacanze estive',
            'description' => 'Prima di partire per le vacanze, ecco la nostra checklist per viaggiare sereni: dai freni al condizionatore, tutto quello che serve.',
            'published_date' => '2026-06-20',
            'image' => 'https://images.pexels.com/photos/34337558/pexels-photo-34337558.jpeg?auto=compress&cs=tinysrgb&w=800'
        ]
    ];
}

// Fetch active services
$servicesList = [];
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM services WHERE active = 1 ORDER BY id ASC");
    $servicesList = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Homepage services fetch error: " . $e->getMessage());
}

if (empty($servicesList)) {
    $servicesList = [
        [
            'id' => 1,
            'name' => 'Diagnosi Elettronica',
            'description' => 'Individuiamo con precisione ogni anomalia usando strumenti diagnostici di ultima generazione compatibili con tutte le marche.',
            'features' => ['Lettura centraline', 'Reset spie', 'Codifica componenti']
        ],
        [
            'id' => 2,
            'name' => 'Tagliando & Manutenzione',
            'description' => 'Manutenzione programmata secondo le specifiche della casa madre per mantenere la garanzia e la sicurezza della tua auto.',
            'features' => ['Cambio olio e filtri', 'Controllo 20 punti', 'Certificazione']
        ],
        [
            'id' => 3,
            'name' => 'Impianto Frenante',
            'description' => 'Sostituzione pastiglie, dischi e liquido freni. Interveniamo su sistemi tradizionali, ABS ed elettronici.',
            'features' => ['Pastiglie e dischi', 'Spurgo liquido', 'Test frenata']
        ],
        [
            'id' => 4,
            'name' => 'Pneumatici & Assetto',
            'description' => 'Montaggio, equilibratura e convergenza con macchinari 3D. Cambio stagionale rapido e custodia gomme.',
            'features' => ['Cambio stagionale', 'Convergenza 3D', 'Custodia gomme']
        ],
        [
            'id' => 5,
            'name' => 'Aria Condizionata',
            'description' => 'Ricarica, sanificazione e riparazione impianti clima. Utilizziamo gas R134a e R1234yf certificati.',
            'features' => ['Ricarica gas', 'Sanificazione ozono', 'Riparazione perdite']
        ],
        [
            'id' => 6,
            'name' => 'Pre-Revisione & Controllo',
            'description' => 'Verifica completa del veicolo prima della revisione ministeriale. Se non passa, non paghi il controllo.',
            'features' => ['Check-up gratuito', 'Controllo emissioni', 'Regolazione fari']
        ]
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

  <!-- 1. Hero Section -->
  <section id="home" class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-[#FDFCFA]">
    <!-- Decorative background glow, Ferrari supercar watermark, and dots grid -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden select-none">
      <!-- Translucent Ferrari Supercar Background Watermark -->
      <div class="absolute right-0 top-0 bottom-0 w-full lg:w-[65%] h-full opacity-30 lg:opacity-35 pointer-events-none flex items-center justify-end">
        <img
          src="https://images.unsplash.com/photo-1592198084033-aade902d1aae?crop=entropy&cs=srgb&fm=jpg&w=1920&q=85"
          alt="Ferrari Supercar Background"
          class="w-full h-full object-cover object-right-center filter drop-shadow-2xl"
          style="-webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.6) 30%, rgba(0,0,0,1) 100%); mask-image: linear-gradient(to right, transparent 0%, rgba(0,0,0,0.6) 30%, rgba(0,0,0,1) 100%);"
        />
      </div>

      <!-- Red Brand Radial Glow & Dot Pattern -->
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[900px] rounded-full bg-gradient-to-br from-brand/15 via-brand/5 to-transparent blur-3xl opacity-80"></div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(11,27,43,0.06)_1px,transparent_0)] [background-size:24px_24px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-5 lg:px-8 relative z-10">
      <div class="grid lg:grid-cols-12 gap-12 items-center">
        
        <!-- Left Hero Content -->
        <div class="lg:col-span-7">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-ink/10 shadow-sm mb-6">
            <i class="fa-solid fa-wand-magic-sparkles text-brand text-xs"></i>
            <span class="text-xs font-extrabold text-ink tracking-wide uppercase">Officina Multimarca a Costa di Mezzate</span>
          </div>

          <h1 class="font-display font-black text-ink text-[44px] sm:text-6xl lg:text-[76px] leading-[0.95] tracking-tight">
            La tua auto,<br />
            nelle mani <span class="relative inline-block">
              <span class="relative z-10 text-brand">giuste</span>
              <svg class="absolute -bottom-2 left-0 w-full" height="14" viewBox="0 0 300 14" fill="none">
                <path d="M2 8 C 80 2, 200 2, 298 8" stroke="#E63946" stroke-width="4" stroke-linecap="round" fill="none"/>
              </svg>
            </span>.
          </h1>

          <p class="mt-6 text-lg text-ink/70 max-w-xl leading-relaxed font-normal">
            Manutenzione, riparazioni e diagnosi elettronica multimarca.
            Interventi precisi, preventivi chiari e nessuna sorpresa in fattura.
          </p>

          <div class="mt-8 flex flex-wrap items-center gap-4">
            <button onclick="openBookingModal()" class="h-14 px-8 rounded-full bg-brand hover:bg-brand-dark text-white font-bold text-base shadow-lg shadow-brand/20 transition-all hover:scale-105 active:scale-95 flex items-center gap-3 group">
              Prenota un intervento
              <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
            </button>
            <a href="#servizi" class="h-14 px-8 rounded-full border-2 border-ink/15 hover:border-ink text-ink font-bold text-base inline-flex items-center transition-colors">
              Scopri i servizi
            </a>
          </div>

          <!-- Trust Badges -->
          <div class="mt-10 flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-3">
              <div class="flex -space-x-2">
                <div class="w-9 h-9 rounded-full border-2 border-white bg-brand text-white flex items-center justify-center font-bold text-xs font-display">HK</div>
                <div class="w-9 h-9 rounded-full border-2 border-white bg-ink text-white flex items-center justify-center font-bold text-xs font-display">HK</div>
                <div class="w-9 h-9 rounded-full border-2 border-white bg-amber-400 text-ink flex items-center justify-center font-bold text-xs"><i class="fa-solid fa-star"></i></div>
                <div class="w-9 h-9 rounded-full border-2 border-white bg-emerald-500 text-white flex items-center justify-center font-bold text-xs"><i class="fa-solid fa-check"></i></div>
              </div>
              <div>
                <div class="flex items-center gap-1 text-amber-400 text-xs font-bold">
                  <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                  <span class="ml-1 text-sm font-black text-ink font-display">4.9</span>
                </div>
                <div class="text-xs font-medium text-ink/60">+2.500 clienti soddisfatti</div>
              </div>
            </div>
            <div class="h-8 w-px bg-ink/10 hidden sm:block"></div>
            <div class="flex items-center gap-2 text-sm text-ink/80 font-semibold">
              <i class="fa-solid fa-shield-halved text-emerald-600 text-base"></i>
              <span>Garanzia 12 mesi</span>
            </div>
          </div>
        </div>

        <!-- Right Hero Visual -->
        <div class="lg:col-span-5 relative">
          <div class="relative aspect-[4/5] rounded-[32px] overflow-hidden shadow-2xl shadow-ink/10 border border-ink/10">
            <img
              src="https://images.unsplash.com/photo-1615906655593-ad0386982a0f?crop=entropy&cs=srgb&fm=jpg&w=1000&q=85"
              alt="Meccanico HK Garage al lavoro"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-ink/50 via-transparent to-transparent"></div>
          </div>

          <!-- Floating Badges -->
          <div class="absolute -left-6 top-10 bg-white rounded-2xl shadow-xl shadow-ink/10 p-4 border border-ink/5 hidden md:flex items-center gap-3 animate-float">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
              <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
              <div class="text-sm font-bold text-ink">Preventivo</div>
              <div class="text-xs text-ink/60">Sempre gratuito</div>
            </div>
          </div>

          <div class="absolute -right-4 bottom-16 bg-white rounded-2xl shadow-xl shadow-ink/10 p-4 border border-ink/5 hidden md:flex items-center gap-3 animate-float-slow">
            <div class="w-11 h-11 rounded-xl bg-brand/10 text-brand flex items-center justify-center text-xl font-bold">
              <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
              <div class="text-sm font-bold text-ink">Consegna 48h</div>
              <div class="text-xs text-ink/60">Sui tagliandi</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Live Workshop Telemetry Status Bar -->
  <div class="bg-metallic-dark border-y border-white/10 text-white py-3.5 relative z-20 shadow-lg">
    <div class="max-w-7xl mx-auto px-5 lg:px-8 flex flex-wrap items-center justify-between gap-4 text-xs font-semibold">
      <div class="flex items-center gap-3">
        <span class="relative flex h-3 w-3">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>
        <span class="font-display font-black tracking-wider text-white">OFFICINA ATTIVA:</span>
        <span class="text-white/70 font-medium">Costa di Mezzate · 4 Postazioni Operative</span>
      </div>

      <div class="hidden md:flex items-center gap-8 text-white/75">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-microchip text-brand"></i>
          <span>Diagnosi ECU 4.0: <strong class="text-white font-bold">Multimarca</strong></span>
        </div>
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-shield-halved text-emerald-400"></i>
          <span>Garanzia Riparazione: <strong class="text-white font-bold">12 Mesi</strong></span>
        </div>
      </div>

      <button onclick="openBookingModal()" class="inline-flex items-center gap-2 text-brand hover:text-white font-extrabold transition-colors">
        <span>Prenota un check-up rapido</span>
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
      </button>
    </div>
  </div>

  <!-- Brand Logos Marquee Section (2 Distinct Subsections: Luxury/Premium & Everyday Commercial) -->
  <section class="py-12 bg-white border-b border-ink/10 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      
      <!-- Main Title -->
      <div class="text-center text-xs font-black text-brand tracking-[0.2em] uppercase mb-10 font-display">
        LAVORIAMO SU TUTTE LE PRINCIPALI MARCHE
      </div>
      
      <!-- Gradient Fades on edges -->
      <div class="absolute left-0 top-20 bottom-0 w-20 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
      <div class="absolute right-0 top-20 bottom-0 w-20 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

      <div class="w-full space-y-10">
        
        <!-- Subsection 1: Premium & Luxury Cars -->
        <div>
          <div class="flex items-center gap-2 mb-4 px-2">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-xs font-black text-amber-600 tracking-widest uppercase font-display">
              MARCHI DI LUSSO, SPORTIVE & PREMIUM
            </span>
          </div>
          
          <div class="animate-marquee flex items-center gap-16 py-2">
            <?php 
            $premiumLuxuryBrands = [
              ['name' => 'Ferrari', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/ferrari.svg'],
              ['name' => 'Lamborghini', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/lamborghini.svg'],
              ['name' => 'Maserati', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/maserati.svg'],
              ['name' => 'Porsche', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/porsche.svg'],
              ['name' => 'Alfa Romeo', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/alfa-romeo.svg'],
              ['name' => 'Audi', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/audi.svg'],
              ['name' => 'BMW', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/bmw.svg'],
              ['name' => 'Mercedes', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/mercedes-benz.svg'],
              ['name' => 'Bentley', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/bentley.svg'],
              ['name' => 'Aston Martin', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/aston-martin.svg']
            ];
            $premiumLoop = array_merge($premiumLuxuryBrands, $premiumLuxuryBrands, $premiumLuxuryBrands);
            foreach ($premiumLoop as $b):
            ?>
              <div class="group flex flex-col items-center justify-center text-center cursor-pointer flex-shrink-0 transition-transform hover:-translate-y-1">
                <div class="w-16 h-10 flex items-center justify-center mb-2.5">
                  <img src="<?php echo $b['logo']; ?>" alt="<?php echo $b['name']; ?> Logo" class="max-w-full max-h-full w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-110 opacity-80 group-hover:opacity-100" loading="lazy" />
                </div>
                <span class="font-display font-black text-sm text-ink/70 group-hover:text-amber-600 tracking-tight transition-colors">
                  <?php echo $b['name']; ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Subsection 2: Day to Day & Everyday Commercial Cars -->
        <div class="pt-6 border-t border-ink/5">
          <div class="flex items-center gap-2 mb-4 px-2">
            <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span>
            <span class="text-xs font-black text-brand tracking-widest uppercase font-display">
              MARCHI COMMERCIALI & AUTO DI TUTTI I GIORNI
            </span>
          </div>

          <div class="animate-marquee-reverse flex items-center gap-16 py-2">
            <?php 
            $everydayBrands = [
              ['name' => 'Fiat', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/fiat.svg'],
              ['name' => 'Volkswagen', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/volkswagen.svg'],
              ['name' => 'Peugeot', 'logo' => 'https://cdn.simpleicons.org/peugeot/0B1B2B'],
              ['name' => 'Renault', 'logo' => 'https://cdn.simpleicons.org/renault/0B1B2B'],
              ['name' => 'Ford', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/ford.svg'],
              ['name' => 'Toyota', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/toyota.svg'],
              ['name' => 'Citroën', 'logo' => 'https://cdn.simpleicons.org/citroen/0B1B2B'],
              ['name' => 'Opel', 'logo' => 'https://cdn.simpleicons.org/opel/0B1B2B'],
              ['name' => 'SEAT', 'logo' => 'https://cdn.simpleicons.org/seat/0B1B2B'],
              ['name' => 'Škoda', 'logo' => 'https://cdn.simpleicons.org/skoda/0B1B2B'],
              ['name' => 'Volvo', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/volvo.svg'],
              ['name' => 'Hyundai', 'logo' => 'https://cdn.simpleicons.org/hyundai/0B1B2B'],
              ['name' => 'Kia', 'logo' => 'https://cdn.simpleicons.org/kia/0B1B2B'],
              ['name' => 'Nissan', 'logo' => 'https://cdn.simpleicons.org/nissan/0B1B2B'],
              ['name' => 'Jeep', 'logo' => 'https://cdn.jsdelivr.net/gh/diegojasso/car-logos-SVG/logos/jeep.svg'],
              ['name' => 'MINI', 'logo' => 'https://cdn.simpleicons.org/mini/0B1B2B']
            ];
            $everydayLoop = array_merge($everydayBrands, $everydayBrands, $everydayBrands);
            foreach ($everydayLoop as $b):
            ?>
              <div class="group flex flex-col items-center justify-center text-center cursor-pointer flex-shrink-0 transition-transform hover:-translate-y-1">
                <div class="w-16 h-10 flex items-center justify-center mb-2.5">
                  <img src="<?php echo $b['logo']; ?>" alt="<?php echo $b['name']; ?> Logo" class="max-w-full max-h-full w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-110 opacity-75 group-hover:opacity-100" loading="lazy" />
                </div>
                <span class="font-display font-black text-sm text-ink/70 group-hover:text-brand tracking-tight transition-colors">
                  <?php echo $b['name']; ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- 2. Services Section -->
  <section id="servizi" class="pt-12 pb-20 lg:pt-16 lg:pb-28 bg-cream">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      
      <!-- Section Header -->
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10 reveal">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 mb-3">
            <span class="w-8 h-1 bg-brand rounded-full"></span>
            <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">I NOSTRI SERVIZI</span>
          </div>
          <h2 class="font-display font-black text-ink text-4xl lg:text-6xl leading-[1] tracking-tight">
            Tutto quello che serve<br />
            alla tua auto, sotto un unico tetto.
          </h2>
        </div>
        <p class="text-ink/60 lg:max-w-sm text-base leading-relaxed">
          Interventi rapidi, componenti originali o equivalenti certificati e garanzia scritta di 12 mesi su ogni riparazione.
        </p>
      </div>

      <!-- Services Grid (Restored Card Grid Layout) -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
        $defaultIcons = ['fa-screwdriver-wrench', 'fa-clipboard-check', 'fa-microchip', 'fa-oil-can', 'fa-shield-halved', 'fa-dharmachakra'];
        $delays = ['delay-100', 'delay-200', 'delay-300', 'delay-400', 'delay-500', 'delay-100'];
        foreach ($servicesList as $idx => $service): 
          // Feature fallback lists if coming from standard DB query
          $features = isset($service['features']) ? $service['features'] : ['Componenti OEM', 'Garanzia 12 Mesi', 'Check-up completo'];
          $serviceName = isset($service['name']) ? $service['name'] : $service['title'];

          // Dynamic context-matching icon logic
          $sNameLower = strtolower($serviceName);
          $icon = isset($defaultIcons[$idx]) ? $defaultIcons[$idx] : 'fa-wrench';
          if (strpos($sNameLower, 'meccanic') !== false || strpos($sNameLower, 'riparazion') !== false) {
            $icon = 'fa-screwdriver-wrench';
          } elseif (strpos($sNameLower, 'tagliand') !== false || strpos($sNameLower, 'manutenzion') !== false) {
            $icon = 'fa-clipboard-check';
          } elseif (strpos($sNameLower, 'diagnos') !== false || strpos($sNameLower, 'elettron') !== false) {
            $icon = 'fa-microchip';
          } elseif (strpos($sNameLower, 'olio') !== false || strpos($sNameLower, 'filtr') !== false) {
            $icon = 'fa-oil-can';
          } elseif (strpos($sNameLower, 'fren') !== false || strpos($sNameLower, 'sospension') !== false) {
            $icon = 'fa-shield-halved';
          } elseif (strpos($sNameLower, 'clima') !== false) {
            $icon = 'fa-snowflake';
          } elseif (strpos($sNameLower, 'gomm') !== false || strpos($sNameLower, 'pneumatic') !== false) {
            $icon = 'fa-dharmachakra';
          }
          $delayClass = isset($delays[$idx]) ? $delays[$idx] : '';
        ?>
          <div class="group relative bg-white/75 backdrop-blur-md rounded-3xl p-6 lg:p-7 border border-white/80 shadow-xl shadow-ink/5 hover:bg-white/95 hover:border-brand/30 hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-500 flex flex-col justify-between reveal <?php echo $delayClass; ?>">
            <div>
              <div class="mb-5">
                <div class="w-12 h-12 rounded-2xl bg-ink text-white flex items-center justify-center text-xl group-hover:bg-brand transition-colors shadow-md">
                  <i class="fa-solid <?php echo $icon; ?>"></i>
                </div>
              </div>

              <h3 class="font-display font-bold text-xl lg:text-2xl text-ink mb-2.5 tracking-tight">
                <?php echo htmlspecialchars($serviceName); ?>
              </h3>

              <p class="text-ink/65 text-sm leading-relaxed mb-4 font-normal">
                <?php echo htmlspecialchars($service['description']); ?>
              </p>

              <!-- Revealable Feature Bullets (Slides up smoothly on hover) -->
              <div class="max-h-0 opacity-0 overflow-hidden pointer-events-none translate-y-3 group-hover:max-h-36 group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto group-hover:mb-4 transition-all duration-500 ease-out">
                <ul class="space-y-2 pt-3 border-t border-ink/10">
                  <?php foreach ($features as $f): ?>
                    <li class="flex items-center gap-2 text-xs font-bold text-ink/80">
                      <span class="w-1.5 h-1.5 rounded-full bg-brand flex-shrink-0"></span>
                      <?php echo htmlspecialchars($f); ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>

            <button onclick="openBookingModal('<?php echo htmlspecialchars($serviceName, ENT_QUOTES); ?>')" class="inline-flex items-center justify-between w-full text-sm font-extrabold text-ink group-hover:text-brand transition-colors pt-4 border-t border-ink/10">
              <span>Richiedi preventivo</span>
              <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </button>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- Interactive Diagnostic & Service Calculator Widget (Box-Free Seamless Editorial Bar) -->
  <section class="py-16 bg-cream text-ink border-y border-ink/10 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-5 lg:px-8 relative z-10 reveal">
      
      <!-- Box-Free Layout -->
      <div class="grid lg:grid-cols-12 gap-10 items-center">
        
        <!-- Left Title & Header -->
        <div class="lg:col-span-5">
          <div class="inline-flex items-center gap-2 mb-3">
            <span class="w-8 h-1 bg-brand rounded-full"></span>
            <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">CALCOLATORE SERVIZIO</span>
          </div>
          <h3 class="font-display font-black text-ink text-3xl lg:text-4xl tracking-tight mb-3">
            Calcola tempo & garanzia per la tua auto.
          </h3>
          <p class="text-ink/70 text-sm leading-relaxed font-normal">
            Seleziona la marca e il servizio desiderato per scoprire la stima di consegna e richiedere direttamente la prenotazione.
          </p>
        </div>

        <!-- Right Controls & Status Strip -->
        <div class="lg:col-span-7 space-y-5">
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold uppercase tracking-wider text-ink/60 mb-2 block font-display">1. Marca Veicolo</label>
              <select id="estimatorBrand" onchange="updateEstimatorResult()" class="w-full h-12 px-4 rounded-full bg-white border border-ink/15 text-ink font-semibold text-sm outline-none focus:border-brand shadow-sm">
                <option value="Audi">Audi</option>
                <option value="BMW">BMW</option>
                <option value="Mercedes">Mercedes-Benz</option>
                <option value="Volkswagen">Volkswagen</option>
                <option value="Fiat">Fiat / Alfa Romeo</option>
                <option value="Porsche">Porsche / Ferrari / Maserati</option>
                <option value="Altro">Altra Marca Multimarca</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-bold uppercase tracking-wider text-ink/60 mb-2 block font-display">2. Tipo di Intervento</label>
              <select id="estimatorService" onchange="updateEstimatorResult()" class="w-full h-12 px-4 rounded-full bg-white border border-ink/15 text-ink font-semibold text-sm outline-none focus:border-brand shadow-sm">
                <option value="tagliando">Tagliando & Cambio Olio</option>
                <option value="diagnosi">Diagnosi Spia / Centralina ECU</option>
                <option value="freni">Controllo Freni & Sospensioni</option>
                <option value="meccanica">Riparazione Meccanica / Motore</option>
                <option value="clima">Ricarica Clima & Filtri</option>
              </select>
            </div>
          </div>

          <!-- Seamless Horizontal Estimator Result Bar (No Card Box) -->
          <div class="pt-5 border-t border-ink/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider flex items-center gap-1.5 mb-1 font-display">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>STIMA CONSEGNA: <strong id="estimatorTime" class="text-ink">Consegna in 24 ore</strong></span>
              </div>
              <div class="text-xs text-ink/60 font-medium">
                Incluso: <span class="text-ink font-semibold">Diagnosi OBD2 + Garanzia 12 Mesi</span>
              </div>
            </div>

            <button onclick="triggerEstimatedBooking()" class="h-12 px-7 rounded-full bg-brand hover:bg-brand-dark text-white font-bold text-xs shadow-lg transition-transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2 flex-shrink-0">
              <span>Prenota questo intervento</span>
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
          </div>
        </div>

      </div>

    </div>
  </section>

  <script>
    function updateEstimatorResult() {
      const service = document.getElementById('estimatorService').value;
      const timeElem = document.getElementById('estimatorTime');
      if (service === 'tagliando') {
        timeElem.textContent = 'Consegna in 24 ore';
      } else if (service === 'diagnosi') {
        timeElem.textContent = 'Esito in giornata (30 min)';
      } else if (service === 'freni') {
        timeElem.textContent = 'Consegna in 24-48 ore';
      } else if (service === 'clima') {
        timeElem.textContent = 'Intervento rapido in 1 ora';
      } else {
        timeElem.textContent = 'Consegna in 24-48 ore';
      }
    }

    function triggerEstimatedBooking() {
      openBookingModal();
    }
  </script>

  <!-- 3. About Section -->
  <section id="chi-siamo" class="py-24 lg:py-32 bg-[#FDFCFA]">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      <div class="grid lg:grid-cols-2 gap-16 items-center">
        
        <!-- Image Side Grid -->
        <div class="relative reveal-left">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-4">
              <div class="aspect-[3/4] rounded-3xl overflow-hidden shadow-lg">
                <img
                  src="https://images.unsplash.com/photo-1625047509248-ec889cbff17f?crop=entropy&cs=srgb&fm=jpg&w=800&q=85"
                  alt="Team HK Garage"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
                />
              </div>
              <div class="aspect-square rounded-3xl bg-ink text-white p-6 flex flex-col justify-between shadow-lg">
                <div class="text-6xl font-display font-black text-brand">15+</div>
                <div class="text-sm font-medium text-white/80 leading-snug">Anni di esperienza sul campo, tra passione e tecnologia.</div>
              </div>
            </div>
            <div class="space-y-4 mt-10">
              <div class="aspect-square rounded-3xl bg-brand text-white p-6 flex flex-col justify-between shadow-lg">
                <div class="text-xs font-bold uppercase tracking-wider text-white/80">Fondatori</div>
                <div>
                  <div class="text-2xl font-display font-black leading-tight">Harshit<br />& Karan</div>
                  <div class="text-xs font-semibold text-white/80 mt-1">Meccanici certificati</div>
                </div>
              </div>
              <div class="aspect-[3/4] rounded-3xl overflow-hidden shadow-lg">
                <img
                  src="https://images.unsplash.com/photo-1676018366904-c083ed678e60?crop=entropy&cs=srgb&fm=jpg&w=800&q=85"
                  alt="Officina HK Garage"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Text Side -->
        <div class="reveal-right">
          <div class="inline-flex items-center gap-2 mb-4">
            <span class="w-8 h-1 bg-brand rounded-full"></span>
            <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">CHI SIAMO</span>
          </div>
          <h2 class="font-display font-black text-ink text-4xl lg:text-5xl leading-[1.05] tracking-tight mb-6">
            Un'officina fatta da meccanici, per automobilisti.
          </h2>
          <p class="text-ink/70 text-base leading-relaxed mb-8 font-normal">
            HK Garage nasce dalla passione di Harshit e Karan per il mondo dell'auto.
            Da oltre 15 anni ci prendiamo cura dei veicoli dei nostri clienti a Costa di
            Mezzate, con un approccio che unisce competenza tecnica, tecnologia diagnostica
            di ultima generazione e un rapporto umano diretto.
          </p>

          <ul class="space-y-4 mb-10">
            <li class="flex items-start gap-3">
              <i class="fa-solid fa-circle-check text-brand text-xl mt-0.5"></i>
              <span class="text-ink font-semibold text-base">Meccanici certificati e continuamente formati</span>
            </li>
            <li class="flex items-start gap-3">
              <i class="fa-solid fa-circle-check text-brand text-xl mt-0.5"></i>
              <span class="text-ink font-semibold text-base">Preventivi trasparenti prima di ogni intervento</span>
            </li>
            <li class="flex items-start gap-3">
              <i class="fa-solid fa-circle-check text-brand text-xl mt-0.5"></i>
              <span class="text-ink font-semibold text-base">Ricambi originali o equivalenti di qualità OEM</span>
            </li>
            <li class="flex items-start gap-3">
              <i class="fa-solid fa-circle-check text-brand text-xl mt-0.5"></i>
              <span class="text-ink font-semibold text-base">Garanzia scritta 12 mesi su ogni riparazione</span>
            </li>
          </ul>

          <!-- Stats Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-8 border-t border-ink/10">
            <div>
              <div class="font-display font-black text-3xl text-ink">15+</div>
              <div class="text-xs font-semibold text-ink/60 mt-1">Anni esperienza</div>
            </div>
            <div>
              <div class="font-display font-black text-3xl text-ink">2.500+</div>
              <div class="text-xs font-semibold text-ink/60 mt-1">Clienti felici</div>
            </div>
            <div>
              <div class="font-display font-black text-3xl text-ink">48h</div>
              <div class="text-xs font-semibold text-ink/60 mt-1">Media consegna</div>
            </div>
            <div>
              <div class="font-display font-black text-3xl text-ink">4.9</div>
              <div class="text-xs font-semibold text-ink/60 mt-1">Stelle Google</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>



  <!-- 4. Gallery Section -->
  <section id="galleria" class="py-24 lg:py-32 bg-cream">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      
      <!-- Header -->
      <div class="text-center mb-14 reveal">
        <div class="inline-flex items-center gap-2 mb-4 justify-center">
          <span class="w-8 h-1 bg-brand rounded-full"></span>
          <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">LA NOSTRA OFFICINA</span>
          <span class="w-8 h-1 bg-brand rounded-full"></span>
        </div>
        <h2 class="font-display font-black text-ink text-4xl lg:text-6xl leading-[1] tracking-tight max-w-3xl mx-auto">
          Uno spazio pensato per il lavoro fatto bene.
        </h2>
      </div>

      <!-- Masonry Gallery -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <?php
        $galleryImages = [
          ['url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', 'title' => 'Officina moderna'],
          ['url' => 'https://images.unsplash.com/photo-1615906655593-ad0386982a0f?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', 'title' => 'Meccanico al lavoro'],
          ['url' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', 'title' => 'Diagnostica avanzata'],
          ['url' => 'https://images.unsplash.com/photo-1676018366904-c083ed678e60?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', 'title' => 'Sollevatore idraulico'],
          ['url' => 'https://images.unsplash.com/photo-1643701079732-3b1c7a797e3d?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', 'title' => 'Motore in revisione'],
          ['url' => 'https://images.pexels.com/photos/8986132/pexels-photo-8986132.jpeg?auto=compress&cs=tinysrgb&w=800', 'title' => 'Controllo qualità'],
          ['url' => 'https://images.pexels.com/photos/8478259/pexels-photo-8478259.jpeg?auto=compress&cs=tinysrgb&w=800', 'title' => 'Interventi tecnici'],
          ['url' => 'https://images.pexels.com/photos/34337558/pexels-photo-34337558.jpeg?auto=compress&cs=tinysrgb&w=800', 'title' => 'Attrezzatura professionale']
        ];
        $galleryDelays = ['delay-100', 'delay-200', 'delay-300', 'delay-400', 'delay-100', 'delay-200', 'delay-300', 'delay-400'];
        foreach ($galleryImages as $idx => $img):
          $spanClass = ($idx === 0 || $idx === 3 || $idx === 5) ? 'aspect-[3/4]' : 'aspect-square';
          $gDelay = isset($galleryDelays[$idx]) ? $galleryDelays[$idx] : '';
        ?>
          <button
            type="button"
            onclick="openLightbox('<?php echo htmlspecialchars($img['url'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($img['title'], ENT_QUOTES); ?>')"
            class="relative group overflow-hidden rounded-2xl bg-ink <?php echo $spanClass; ?> text-left shadow-sm focus:outline-none reveal-scale <?php echo $gDelay; ?>"
          >
            <img
              src="<?php echo htmlspecialchars($img['url']); ?>"
              alt="<?php echo htmlspecialchars($img['title']); ?>"
              class="w-full h-full object-cover group-hover:scale-110 group-hover:opacity-90 transition-all duration-500"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute bottom-4 left-4 right-4 text-white opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
              <div class="text-sm font-bold font-display"><?php echo htmlspecialchars($img['title']); ?></div>
            </div>
          </button>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- 5. Testimonials Section -->
  <section id="recensioni" class="py-24 lg:py-32 bg-[#FDFCFA]">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      
      <div class="grid lg:grid-cols-12 gap-12 mb-14 items-end reveal">
        <div class="lg:col-span-7">
          <div class="inline-flex items-center gap-2 mb-4">
            <span class="w-8 h-1 bg-brand rounded-full"></span>
            <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">RECENSIONI VERIFICATE</span>
          </div>
          <h2 class="font-display font-black text-ink text-4xl lg:text-6xl leading-[1] tracking-tight">
            Chi ci prova,<br />
            torna sempre da noi.
          </h2>
        </div>
        <div class="lg:col-span-5">
          <div class="bg-ink text-white rounded-3xl p-6 flex items-center gap-5 shadow-xl">
            <div class="text-6xl font-display font-black text-brand">4.9</div>
            <div>
              <div class="flex gap-1 text-amber-400 text-sm mb-1">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
              </div>
              <div class="text-base font-bold font-display">Recensioni Google</div>
              <div class="text-xs text-white/60 font-medium">Basato su +380 recensioni di clienti reali</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonials Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $testimonials = [
          [
            'name' => 'Marco Bianchi',
            'role' => 'Cliente da 4 anni',
            'rating' => 5,
            'text' => 'Sempre puntuali, prezzi chiari e nessuna sorpresa in fattura. Harshit e Karan sono meccanici seri e appassionati. Consigliatissimo.',
            'initial' => 'MB'
          ],
          [
            'name' => 'Giulia Ferrari',
            'role' => 'BMW Serie 3',
            'rating' => 5,
            'text' => 'Ho provato tante officine, ma HK Garage è l\'unica dove mi sento davvero ascoltata. Mi spiegano tutto prima di intervenire.',
            'initial' => 'GF'
          ],
          [
            'name' => 'Alessandro Rossi',
            'role' => 'Audi A4',
            'rating' => 5,
            'text' => 'Diagnosi elettronica precisa che due officine non erano riuscite a fare. Risolto in mezza giornata. Professionisti veri.',
            'initial' => 'AR'
          ],
          [
            'name' => 'Sara Colombo',
            'role' => 'Fiat 500',
            'rating' => 5,
            'text' => 'Cordiali, veloci e onesti. Non ti fanno mai spendere più del necessario. Finalmente un\'officina di fiducia vicino casa.',
            'initial' => 'SC'
          ]
        ];
        $tDelays = ['delay-100', 'delay-200', 'delay-300', 'delay-400'];
        foreach ($testimonials as $tIdx => $t):
          $tDelay = isset($tDelays[$tIdx]) ? $tDelays[$tIdx] : '';
        ?>
          <div class="py-6 border-t border-ink/10 flex flex-col justify-between reveal <?php echo $tDelay; ?>">
            <div>
              <div class="flex items-center justify-between mb-4">
                <i class="fa-solid fa-quote-left text-brand text-xl"></i>
                <div class="flex gap-0.5 text-amber-400 text-xs">
                  <?php for($i=0;$i<$t['rating'];$i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                </div>
              </div>
              <p class="text-ink/80 text-sm leading-relaxed mb-6 italic">
                “<?php echo htmlspecialchars($t['text']); ?>”
              </p>
            </div>
            
            <div class="flex items-center gap-3 pt-4 border-t border-ink/5">
              <div class="w-10 h-10 rounded-full bg-ink text-white flex items-center justify-center font-bold text-xs font-display">
                <?php echo $t['initial']; ?>
              </div>
              <div>
                <div class="font-bold text-ink text-sm font-display"><?php echo htmlspecialchars($t['name']); ?></div>
                <div class="text-xs text-ink/50 font-medium"><?php echo htmlspecialchars($t['role']); ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- 6. News Section (Dynamic DB integration) -->
  <section id="news" class="py-24 lg:py-32 bg-cream">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14">
        <div>
          <div class="inline-flex items-center gap-2 mb-4">
            <span class="w-8 h-1 bg-brand rounded-full"></span>
            <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">NEWS & AGGIORNAMENTI</span>
          </div>
          <h2 class="font-display font-black text-ink text-4xl lg:text-6xl leading-[1] tracking-tight">
            Notizie dall'officina.
          </h2>
        </div>
        <a href="news.php" class="inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-brand-dark transition-colors">
          Vedi tutte le notizie <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
      </div>

      <!-- News Cards Grid (Modern UI Card Layout with Translucent Mechanic Photos) -->
      <div class="grid md:grid-cols-3 gap-8">
        <?php 
        $mechanicBgs = [
          'https://images.unsplash.com/photo-1615906655593-ad0386982a0f?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', // Mechanic at work
          'https://images.unsplash.com/photo-1580273916550-e323be2ae537?crop=entropy&cs=srgb&fm=jpg&w=800&q=85', // Workshop tools & wrenches
          'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?crop=entropy&cs=srgb&fm=jpg&w=800&q=85'  // ECU diagnostic tools
        ];
        foreach ($latestNews as $nIdx => $item): 
          $bgImg = isset($mechanicBgs[$nIdx % count($mechanicBgs)]) ? $mechanicBgs[$nIdx % count($mechanicBgs)] : $mechanicBgs[0];
        ?>
          <div class="group relative bg-white rounded-3xl overflow-hidden border border-ink/10 shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col justify-between cursor-pointer" onclick="openNewsModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)">
            
            <!-- Translucent Mechanic & Tools Background Photo Layer inside Card -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden rounded-3xl z-0">
              <img src="<?php echo $bgImg; ?>" alt="Mechanic background" class="w-full h-full object-cover opacity-20 mix-blend-luminosity filter contrast-125 group-hover:scale-110 transition-transform duration-700" />
              <div class="absolute inset-0 bg-gradient-to-b from-white/95 via-white/85 to-white/95 backdrop-blur-[1px]"></div>
            </div>

            <!-- Content Layer -->
            <div class="relative z-10">
              <?php if (!empty($item['image'])): ?>
                <div class="w-full h-52 overflow-hidden relative bg-slate-100">
                  <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
              <?php endif; ?>
              
              <div class="p-7">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ink/5 border border-ink/5 text-ink/80 text-xs font-bold font-display mb-3 shadow-sm">
                  <i class="fa-regular fa-calendar text-brand text-xs"></i>
                  <span><?php echo date('d/m/Y', strtotime($item['published_date'])); ?></span>
                </div>

                <h3 class="font-display font-black text-xl text-ink mb-3 leading-snug tracking-tight group-hover:text-brand transition-colors line-clamp-2">
                  <?php echo htmlspecialchars($item['title']); ?>
                </h3>

                <!-- Strictly 2 Lines Only -->
                <p class="text-ink/75 text-sm leading-relaxed line-clamp-2 font-normal">
                  <?php echo htmlspecialchars($item['description']); ?>
                </p>
              </div>
            </div>
            
            <div class="relative z-10 px-7 pb-7 pt-3 border-t border-ink/10 flex items-center justify-between bg-white/40">
              <span class="inline-flex items-center gap-2 text-sm font-extrabold text-brand group-hover:text-brand-dark transition-colors">
                <span>Leggi l'articolo</span>
                <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
              </span>
              <span class="w-8 h-8 rounded-full bg-cream text-ink/50 group-hover:bg-brand group-hover:text-white flex items-center justify-center text-xs transition-colors shadow-sm border border-ink/5">
                <i class="fa-solid fa-book-open"></i>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- 7. Contact Banner & Info Grid -->
  <section id="contatti" class="py-16 lg:py-24 bg-[#FDFCFA]">
    <div class="max-w-7xl mx-auto px-5 lg:px-8">
      
      <!-- CTA Banner with Automobile Sector Background Collage -->
      <div class="relative bg-metallic-dark border border-white/15 rounded-[36px] overflow-hidden p-10 lg:p-16 mb-16 shadow-2xl group reveal-scale">
        
        <!-- Automobile Sector Visual Background Collage -->
        <div class="absolute inset-0 pointer-events-none select-none overflow-hidden z-0">
          <!-- Image 1: Modern Mechanic Workshop (Left) -->
          <img
            src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?crop=entropy&cs=srgb&fm=jpg&w=1200&q=85"
            alt="Mechanic Workshop"
            class="absolute left-0 top-0 w-3/5 h-full object-cover opacity-25 mix-blend-luminosity filter contrast-125 transition-transform duration-1000 group-hover:scale-105"
            style="-webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0.9) 0%, transparent 100%); mask-image: linear-gradient(to right, rgba(0,0,0,0.9) 0%, transparent 100%);"
          />

          <!-- Image 2: Supercar & Luxury Automotive (Right) -->
          <img
            src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?crop=entropy&cs=srgb&fm=jpg&w=1200&q=85"
            alt="Supercar Automobile"
            class="absolute right-0 top-0 w-3/5 h-full object-cover opacity-30 mix-blend-luminosity filter brightness-110 transition-transform duration-1000 group-hover:scale-105"
            style="-webkit-mask-image: linear-gradient(to left, rgba(0,0,0,0.95) 10%, transparent 100%); mask-image: linear-gradient(to left, rgba(0,0,0,0.95) 10%, transparent 100%);"
          />

          <!-- Image 3: Brakes & Servicing Diagnostic Detail (Center Overlay) -->
          <img
            src="https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?crop=entropy&cs=srgb&fm=jpg&w=1200&q=85"
            alt="Automobile Servicing"
            class="absolute left-1/3 top-0 w-1/3 h-full object-cover opacity-15 mix-blend-overlay"
          />

          <!-- Dark Gradient & Glass Vignette (Guarantees Text Legibility) -->
          <div class="absolute inset-0 bg-gradient-to-r from-[#070708]/92 via-[#070708]/80 to-[#070708]/88"></div>

          <!-- Neon Automotive Glows & Radial Dot Tech Pattern -->
          <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
          <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.08)_1px,transparent_0)] [background-size:24px_24px] opacity-40"></div>
        </div>

        <!-- Content Layer -->
        <div class="relative z-10 grid lg:grid-cols-2 gap-10 items-center">
          <div>
            <div class="inline-flex items-center gap-2 mb-4 px-3 py-1 rounded-full bg-white/10 border border-white/15 backdrop-blur-sm">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-xs font-black text-white/90 tracking-[0.2em] uppercase font-display">PRONTO A PARTIRE?</span>
            </div>
            <h2 class="font-display font-black text-white text-4xl lg:text-6xl leading-[1] tracking-tight mb-4">
              Prenota il tuo intervento oggi.
            </h2>
            <p class="text-white/75 text-base leading-relaxed max-w-md font-medium">
              Preventivo gratuito, garanzia 12 mesi. Ti ricontattiamo entro 24 ore per confermare l'appuntamento.
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-4 lg:justify-end">
            <button onclick="openBookingModal()" class="h-14 px-8 rounded-full bg-white hover:bg-white/90 text-ink font-black text-base shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
              <i class="fa-solid fa-calendar-check text-sm text-ink"></i>
              Prenota adesso
            </button>
            <a href="tel:+390351234567" class="h-14 px-8 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white font-bold text-base inline-flex items-center justify-center gap-2 transition-all hover:scale-105">
              <i class="fa-solid fa-phone text-sm"></i>
              Chiamaci
            </a>
          </div>
        </div>
      </div>

      <!-- Info Details (Minimal Borderless Divided Strip) -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 divide-y md:divide-y-0 md:divide-x divide-ink/10 pt-8 border-t border-ink/10">
        <div class="pt-4 md:pt-0 md:px-4 reveal delay-100">
          <div class="text-brand text-xl font-bold mb-2">
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div class="text-xs font-black text-ink/40 tracking-wider uppercase mb-1 font-display">INDIRIZZO</div>
          <div class="text-ink font-bold text-sm leading-relaxed">
            Via Consortile della Conta, 3<br>24060 Costa di Mezzate (BG)
          </div>
        </div>

        <div class="pt-4 md:pt-0 md:px-4 reveal delay-200">
          <div class="text-brand text-xl font-bold mb-2">
            <i class="fa-solid fa-phone"></i>
          </div>
          <div class="text-xs font-black text-ink/40 tracking-wider uppercase mb-1 font-display">TELEFONO</div>
          <a href="tel:+390351234567" class="text-ink font-bold text-sm hover:text-brand transition-colors">+39 035 123 4567</a>
        </div>

        <div class="pt-4 md:pt-0 md:px-4 reveal delay-300">
          <div class="text-brand text-xl font-bold mb-2">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div class="text-xs font-black text-ink/40 tracking-wider uppercase mb-1 font-display">EMAIL</div>
          <a href="mailto:appointments@hkgarage.it" class="text-ink font-bold text-sm hover:text-brand transition-colors">appointments@hkgarage.it</a>
        </div>

        <div class="pt-4 md:pt-0 md:px-4 reveal delay-400">
          <div class="text-brand text-xl font-bold mb-2">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div class="text-xs font-black text-ink/40 tracking-wider uppercase mb-2 font-display">ORARI APERTURA</div>
          <div class="text-xs text-ink/80 leading-relaxed space-y-0.5 font-medium">
            <div><strong class="text-ink">Lun - Ven:</strong> 08:00 - 12:30 / 14:00 - 18:30</div>
            <div><strong class="text-ink">Sabato:</strong> 08:00 - 12:00</div>
            <div><strong class="text-brand font-bold">Domenica:</strong> Chiuso</div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- 8. Interactive Booking Modal -->
  <div id="bookingModal" class="fixed inset-0 z-[100] bg-[#070708]/95 backdrop-blur-md flex items-center justify-center p-4 hidden animate-fade-in" onclick="closeBookingModal()">
    <div class="bg-white max-w-lg w-full rounded-3xl overflow-hidden shadow-2xl border border-white/20 relative max-h-[85vh] flex flex-col overscroll-contain" onclick="event.stopPropagation()">
      
      <!-- Close Button -->
      <button type="button" onclick="closeBookingModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center z-30 transition-colors">
        <i class="fa-solid fa-xmark"></i>
      </button>

      <!-- Modal Header (Locked at top) -->
      <div class="bg-metallic-dark text-white p-5 relative flex-shrink-0 border-b border-white/10">
        <div class="flex items-center gap-2 mb-1">
          <i class="fa-solid fa-calendar-days text-brand text-xs"></i>
          <span class="text-[11px] font-black text-brand tracking-[0.2em] uppercase font-display">PRENOTA UN INTERVENTO</span>
        </div>
        <h3 class="font-display font-black text-xl text-white leading-tight">
          Fissa il tuo appuntamento
        </h3>
        <p class="text-xs text-white/60 mt-0.5">
          Ti inviamo la conferma via email e ti contattiamo per ogni dettaglio.
        </p>
      </div>

      <!-- Booking Form (Scrollable Container) -->
      <form id="modalBookingForm" action="api/book-appointment.php" method="POST" class="p-5 space-y-3 bg-white flex-1 overflow-y-auto no-scrollbar overscroll-contain">
        <div class="grid grid-cols-2 gap-2.5">
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Nome e cognome *</label>
            <input type="text" name="customer_name" required placeholder="Mario Rossi" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none" />
          </div>
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Telefono *</label>
            <input type="tel" name="phone" required placeholder="+39 340 000000" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none" />
          </div>
        </div>

        <div>
          <label class="text-xs font-bold text-ink mb-1 block">Email *</label>
          <input type="email" name="email" required placeholder="mario@email.it" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none" />
        </div>

        <div>
          <label class="text-xs font-bold text-ink mb-1 block">Servizio richiesto *</label>
          <select id="modalServiceSelect" name="service_id" required class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none bg-white">
            <option value="">Seleziona un servizio</option>
            <?php foreach ($servicesList as $s): ?>
              <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars(isset($s['name']) ? $s['name'] : $s['title']); ?></option>
            <?php endforeach; ?>
            <option value="other">Altro (Specificare)</option>
          </select>
        </div>

        <!-- Conditional Custom Service Field for 'Altro' -->
        <div id="modalOtherServiceGroup" class="hidden">
          <label class="text-xs font-bold text-brand mb-1 block"><i class="fa-solid fa-[#18181B] fa-pen-to-square mr-1"></i> Specificare il servizio richiesto *</label>
          <input type="text" id="modalCustomService" name="custom_service" placeholder="Es. Sostituzione tergicristalli, diagnosi rumore, lucidatura fari..." class="w-full h-10 px-3 rounded-xl border border-brand/30 bg-brand/5 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none font-medium" />
        </div>

        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Marca *</label>
            <input type="text" name="vehicle_brand" required placeholder="Es. Audi" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none" />
          </div>
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Modello *</label>
            <input type="text" name="vehicle_model" required placeholder="Es. A4" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none" />
          </div>
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Targa *</label>
            <input type="text" name="vehicle_registration" required placeholder="AA123BB" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none uppercase" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Data *</label>
            <input type="text" id="modalBookingDate" name="booking_date" required placeholder="Seleziona data" class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none bg-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-ink mb-1 block">Orario *</label>
            <select id="modalBookingTime" name="booking_time" required class="w-full h-10 px-3 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none bg-white">
              <option value="">Seleziona orario</option>
            </select>
          </div>
        </div>

        <div>
          <label class="text-xs font-bold text-ink mb-1 block">Note aggiuntive</label>
          <textarea name="notes" placeholder="Descrivi brevemente il problema..." rows="2" class="w-full p-2.5 rounded-xl border border-ink/15 text-sm focus:border-brand focus:ring-1 focus:ring-brand outline-none resize-none"></textarea>
        </div>

        <button type="submit" id="modalSubmitBtn" class="w-full h-11 bg-brand hover:bg-brand-dark text-white font-bold rounded-full text-sm shadow-md transition-all hover:scale-[1.01] flex items-center justify-center gap-2 mt-1">
          <span>Invia prenotazione</span>
          <i class="fa-solid fa-paper-plane text-xs"></i>
        </button>

        <p class="text-[11px] text-ink/50 text-center font-medium pt-1">
          Riceverai immediatamente un'email di conferma con i dettagli dell'appuntamento.
        </p>
      </form>
    </div>
  </div>

  <!-- Lightbox Modal for Gallery -->
  <div id="lightboxModal" class="fixed inset-0 z-[120] bg-ink/90 backdrop-blur-md flex items-center justify-center p-6 hidden animate-fade-in" onclick="closeLightbox()">
    <button type="button" onclick="closeLightbox()" class="absolute top-6 right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
      <i class="fa-solid fa-xmark text-xl"></i>
    </button>
    <div class="max-w-4xl max-h-[85vh] text-center" onclick="event.stopPropagation()">
      <img id="lightboxImg" src="" alt="" class="max-w-full max-h-[75vh] rounded-2xl shadow-2xl object-contain mx-auto" />
      <div id="lightboxCaption" class="mt-4 text-white text-lg font-bold font-display"></div>
    </div>
  </div>

  <!-- News Article Reader Modal -->
  <div id="newsModal" class="fixed inset-0 z-[110] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 lg:p-6 hidden animate-fade-in" onclick="closeNewsModal()">
    <div class="bg-white max-w-2xl w-full rounded-3xl overflow-hidden shadow-2xl border border-ink/10 relative max-h-[90vh] flex flex-col overscroll-contain" onclick="event.stopPropagation()">
      
      <!-- Close Button -->
      <button type="button" onclick="closeNewsModal()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center z-30 transition-colors shadow-lg">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>

      <!-- Scrollable Container -->
      <div class="overflow-y-auto custom-scrollbar">
        
        <!-- Hero Banner Image (if available) -->
        <div id="newsModalImgContainer" class="w-full h-64 sm:h-72 overflow-hidden relative bg-slate-100 hidden">
          <img id="newsModalImg" src="" alt="" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
          <div class="absolute bottom-4 left-6 right-6">
            <span id="newsModalBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand text-white text-xs font-bold font-display shadow-md">
              <i class="fa-regular fa-calendar"></i>
              <span id="newsModalDateBanner"></span>
            </span>
          </div>
        </div>

        <!-- Article Content Body -->
        <div class="p-6 sm:p-8">
          <div id="newsModalDateOnly" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-ink/5 text-ink/70 text-xs font-bold font-display mb-3">
            <i class="fa-regular fa-calendar text-brand text-xs"></i>
            <span id="newsModalDate"></span>
          </div>

          <h2 id="newsModalTitle" class="font-display font-black text-2xl sm:text-3xl text-ink leading-snug tracking-tight mb-6"></h2>

          <div id="newsModalContent" class="text-ink/80 text-base leading-relaxed space-y-4 font-normal whitespace-pre-line border-t border-ink/10 pt-6"></div>

          <!-- Bottom Actions -->
          <div class="mt-8 pt-6 border-t border-ink/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <button onclick="closeNewsModal(); openBookingModal();" class="w-full sm:w-auto h-12 px-7 rounded-full bg-brand hover:bg-brand-dark text-white font-bold text-sm shadow-lg transition-transform hover:scale-105 flex items-center justify-center gap-2">
              <i class="fa-solid fa-calendar-check text-xs"></i>
              <span>Prenota per questa assistenza</span>
            </button>
            
            <button onclick="closeNewsModal()" class="w-full sm:w-auto h-12 px-6 rounded-full border border-ink/15 hover:bg-ink/5 text-ink font-bold text-sm transition-colors">
              Chiudi articolo
            </button>
          </div>
        </div>

      </div>

    </div>
  </div>

  <script>
    function openNewsModal(item) {
      if (!item) return;
      document.getElementById('newsModalTitle').textContent = item.title || '';
      
      const formattedDate = item.published_date ? new Date(item.published_date).toLocaleDateString('it-IT', { day: '2-digit', month: 'long', year: 'numeric' }) : '';
      document.getElementById('newsModalDate').textContent = formattedDate;
      document.getElementById('newsModalDateBanner').textContent = formattedDate;
      
      document.getElementById('newsModalContent').textContent = item.description || '';

      const imgContainer = document.getElementById('newsModalImgContainer');
      const imgElem = document.getElementById('newsModalImg');
      const dateOnlyBadge = document.getElementById('newsModalDateOnly');

      if (item.image && item.image.trim() !== '') {
        imgElem.src = item.image;
        imgContainer.classList.remove('hidden');
        dateOnlyBadge.classList.add('hidden');
      } else {
        imgContainer.classList.add('hidden');
        dateOnlyBadge.classList.remove('hidden');
      }

      const modal = document.getElementById('newsModal');
      modal.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }

    function closeNewsModal() {
      const modal = document.getElementById('newsModal');
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


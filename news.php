<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';

$pageTitle = "News & Aggiornamenti | HK Garage";

$allNews = [];
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM news ORDER BY published_date DESC, id DESC");
    $allNews = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("News page fetch error: " . $e->getMessage());
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Header -->
<div class="pt-32 pb-12 bg-cream border-b border-ink/5">
  <div class="max-w-7xl mx-auto px-5 lg:px-8 text-center">
    <div class="inline-flex items-center gap-2 mb-3 justify-center">
      <span class="w-8 h-1 bg-brand rounded-full"></span>
      <span class="text-xs font-extrabold text-brand tracking-[0.2em] uppercase font-display">ULTIME NOTIZIE</span>
      <span class="w-8 h-1 bg-brand rounded-full"></span>
    </div>
    <h1 class="font-display font-black text-ink text-4xl lg:text-5xl tracking-tight mb-3">
      News & Aggiornamenti dall'officina
    </h1>
    <p class="text-ink/60 font-medium max-w-xl mx-auto text-base">
      Resta sempre aggiornato sulle novità di HK Garage, consigli tecnici e scadenze normative auto.
    </p>
  </div>
</div>

<main class="py-16 bg-[#FDFCFA]">
  <div class="max-w-7xl mx-auto px-5 lg:px-8">
    <?php if (empty($allNews)): ?>
      <div class="bg-white p-12 rounded-3xl text-center shadow-sm border border-ink/10 max-w-lg mx-auto">
        <i class="fa-regular fa-newspaper text-6xl text-ink/20 mb-4"></i>
        <h3 class="font-display font-bold text-xl text-ink mb-1">Nessuna news pubblicata</h3>
        <p class="text-ink/60 text-sm font-medium">Torna a trovarci presto per scoprire i nuovi aggiornamenti!</p>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($allNews as $item): ?>
          <article id="news-<?php echo $item['id']; ?>" class="bg-white rounded-3xl overflow-hidden shadow-sm border border-ink/10 hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
            <div>
              <?php if (!empty($item['image'])): ?>
                <div class="w-full h-52 overflow-hidden bg-ink/5 relative">
                  <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                </div>
              <?php endif; ?>
              <div class="p-7">
                <div class="text-xs font-bold text-brand uppercase tracking-wider mb-2 font-display">
                  <i class="fa-regular fa-calendar mr-1.5"></i>
                  <?php echo date('d/m/Y', strtotime($item['published_date'])); ?>
                </div>
                <h2 class="font-display font-bold text-xl text-ink mb-3 leading-snug">
                  <?php echo htmlspecialchars($item['title']); ?>
                </h2>
                <div class="text-ink/70 font-normal text-sm leading-relaxed whitespace-pre-line">
                  <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                </div>
              </div>
            </div>
            
            <div class="px-7 pb-7 pt-2">
              <button onclick="openBookingModal()" class="w-full bg-brand hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider py-3.5 px-4 rounded-full shadow-md transition-all hover:scale-[1.01] flex items-center justify-center gap-2">
                <span>Prenota intervento</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
              </button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


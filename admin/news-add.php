<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = "Nuova Notizia";

$error = '';
$title = '';
$description = '';
$image = '';
$publishedDate = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = trim($_POST['title'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $image         = trim($_POST['image'] ?? '');
    $publishedDate = trim($_POST['published_date'] ?? date('Y-m-d'));

    if (empty($title) || empty($description) || empty($publishedDate)) {
        $error = 'Compila i campi obbligatori (Titolo, Descrizione e Data di pubblicazione).';
    } else {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("
                INSERT INTO news (title, description, image, published_date)
                VALUES (:title, :description, :image, :published_date)
            ");
            $stmt->execute([
                ':title'          => $title,
                ':description'    => $description,
                ':image'          => $image,
                ':published_date' => $publishedDate
            ]);

            header("Location: news.php?msg=added");
            exit;
        } catch (Exception $e) {
            $error = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/admin-header.php';
?>

<div class="mb-6">
  <a href="news.php" class="text-xs font-bold text-gray-500 hover:text-[#d32f2f] uppercase transition-colors">
    &larr; Torna all'elenco news
  </a>
</div>

<div class="max-w-2xl bg-white p-8 rounded-xl shadow-sm border border-gray-200">
  <h2 class="text-xl font-black uppercase text-[#1a1c1e] mb-6 border-b pb-3">Pubblica Nuova News</h2>

  <?php if (!empty($error)): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-[#d32f2f] p-4 rounded text-red-700 text-xs font-bold">
      <?php echo htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="news-add.php" class="space-y-6">
    <div>
      <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Titolo Notizia *</label>
      <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required placeholder="Es. Nuovi Servizi di Diagnosi Computerizzata 2026" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-[#d32f2f]">
    </div>

    <div>
      <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Data Pubblicazione *</label>
      <input type="date" name="published_date" value="<?php echo htmlspecialchars($publishedDate); ?>" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm font-semibold focus:ring-2 focus:ring-[#d32f2f]">
    </div>

    <div>
      <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">URL Immagine (Unsplash, ImgBB, CDN o locale)</label>
      <input type="url" name="image" value="<?php echo htmlspecialchars($image); ?>" placeholder="https://images.unsplash.com/photo-..." class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-[#d32f2f]">
      <p class="text-[11px] text-gray-400 mt-1">Inserisci il link completo all'immagine di copertina.</p>
    </div>

    <div>
      <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Contenuto / Descrizione *</label>
      <textarea name="description" rows="6" required placeholder="Inserisci il testo completo della notizia che apparirà nella card della homepage..." class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-[#d32f2f]"><?php echo htmlspecialchars($description); ?></textarea>
    </div>

    <div class="flex gap-4 pt-4 border-t">
      <button type="submit" class="bg-[#d32f2f] hover:bg-[#b71c1c] text-white font-extrabold uppercase text-xs px-6 py-3.5 rounded-lg shadow-lg transition-transform hover:-translate-y-0.5">
        <i class="fa-solid fa-floppy-disk mr-1"></i> Pubblica Notizia
      </button>
      <a href="news.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs uppercase px-6 py-3.5 rounded-lg transition-colors">
        Annulla
      </a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/admin-footer.php'; ?>

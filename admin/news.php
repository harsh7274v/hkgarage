<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = "Gestione News Homepage";

$actionMessage = '';
$actionError = '';

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $newsId = intval($_POST['news_id'] ?? 0);
    if ($newsId > 0) {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("DELETE FROM news WHERE id = :id");
            $stmt->execute([':id' => $newsId]);
            $actionMessage = "News #$newsId eliminata con successo!";
        } catch (Exception $e) {
            $actionError = "Errore durante l'eliminazione: " . $e->getMessage();
        }
    }
}

// Fetch all news
$newsList = [];
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM news ORDER BY published_date DESC, id DESC");
    $newsList = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Admin news fetch error: " . $e->getMessage());
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

<!-- Top Actions Bar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
  <div>
    <p class="text-xs text-gray-500 font-semibold">Le news pubblicate qui verranno visualizzate automaticamente nella Homepage e nella pagina News del sito pubblico.</p>
  </div>
  <a href="news-add.php" class="bg-[#d32f2f] hover:bg-[#b71c1c] text-white font-extrabold text-xs uppercase px-5 py-3 rounded-lg shadow-lg transition-transform hover:-translate-y-0.5 flex items-center gap-2">
    <i class="fa-solid fa-plus text-sm"></i> Pubblica Nuova News
  </a>
</div>

<!-- News Table Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="p-6 border-b border-gray-200 flex justify-between items-center">
    <h3 class="font-black text-lg uppercase text-[#1a1c1e]">Notizie Pubblicate (<?php echo count($newsList); ?>)</h3>
  </div>

  <div class="p-6 overflow-x-auto">
    <table id="newsTable" class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="bg-gray-100 text-gray-700 font-extrabold uppercase text-xs">
          <th class="p-3 rounded-l">Immagine</th>
          <th class="p-3">Titolo Notizia</th>
          <th class="p-3">Descrizione</th>
          <th class="p-3">Data Pubblicazione</th>
          <th class="p-3 text-right rounded-r">Azioni</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 font-medium">
        <?php foreach ($newsList as $item): ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="p-3">
              <?php if (!empty($item['image'])): ?>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="News Thumb" class="w-16 h-12 object-cover rounded shadow-sm">
              <?php else: ?>
                <div class="w-16 h-12 bg-gray-100 text-gray-400 rounded flex items-center justify-center text-xs">No Img</div>
              <?php endif; ?>
            </td>

            <td class="p-3 font-extrabold text-[#1a1c1e]">
              <?php echo htmlspecialchars($item['title']); ?>
            </td>

            <td class="p-3 text-xs text-gray-600 max-w-xs truncate">
              <?php echo htmlspecialchars($item['description']); ?>
            </td>

            <td class="p-3 text-xs font-bold text-gray-700 whitespace-nowrap">
              <i class="fa-regular fa-calendar text-[#d32f2f] mr-1"></i>
              <?php echo date('d/m/Y', strtotime($item['published_date'])); ?>
            </td>

            <td class="p-3 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-2">
                <a href="news-edit.php?id=<?php echo $item['id']; ?>" class="bg-gray-100 hover:bg-[#1a1c1e] hover:text-white text-gray-800 text-xs font-extrabold px-3 py-1.5 rounded transition-colors">
                  <i class="fa-solid fa-pen-to-square"></i> Modifica
                </a>

                <button type="button" onclick="confirmDeleteNews(<?php echo $item['id']; ?>)" class="bg-red-50 hover:bg-red-600 hover:text-white text-[#d32f2f] text-xs font-extrabold px-3 py-1.5 rounded transition-colors">
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
<form id="deleteNewsForm" method="POST" action="news.php" class="hidden">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" id="delete_news_id" name="news_id" value="">
</form>

<script>
$(document).ready(function() {
  $('#newsTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/it-IT.json'
    },
    order: [[3, 'desc']],
    pageLength: 10
  });
});

function confirmDeleteNews(id) {
  Swal.fire({
    title: 'Eliminare la Notizia?',
    text: `Stai per rimuovere definitivamente la notizia #${id}.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d32f2f',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Sì, elimina!',
    cancelButtonText: 'Annulla'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('delete_news_id').value = id;
      document.getElementById('deleteNewsForm').submit();
    }
  });
}
</script>

<?php require_once __DIR__ . '/admin-footer.php'; ?>

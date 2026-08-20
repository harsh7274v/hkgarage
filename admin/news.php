<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = "News";

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

<!-- Top Actions Bar -->
<div class="bg-white p-6 rounded-3xl border border-black/5 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
  <div>
    <p class="text-xs text-ink/60 font-medium">Le news pubblicate qui verranno visualizzate automaticamente nella Homepage e nella pagina News del sito pubblico.</p>
  </div>
  <a href="news-add.php" class="bg-[#18181B] hover:bg-black text-white font-bold text-xs px-6 py-3 rounded-full shadow-md transition-all hover:scale-105 flex items-center gap-2 flex-shrink-0">
    <i class="fa-solid fa-plus text-xs"></i> Pubblica Nuova News
  </a>
</div>

<!-- News Table Card -->
<div class="bg-white rounded-3xl shadow-sm border border-black/5 overflow-hidden p-7">

  <div class="overflow-x-auto">
    <table id="newsTable" class="w-full text-left text-sm border-collapse">
      <thead>
        <tr class="text-xs font-bold text-ink/40 uppercase border-b border-black/5 pb-3">
          <th class="pb-3 font-display">Immagine</th>
          <th class="pb-3 font-display">Titolo Notizia</th>
          <th class="pb-3 font-display">Descrizione</th>
          <th class="pb-3 font-display">Data Pubblicazione</th>
          <th class="pb-3 font-display text-right">Azioni</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-black/5 font-medium text-ink">
        <?php foreach ($newsList as $item): ?>
          <tr class="hover:bg-black/[0.02] transition-colors">
            <td class="py-4">
              <?php if (!empty($item['image'])): ?>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="News Thumb" class="w-16 h-12 object-cover rounded-2xl shadow-sm border border-black/5">
              <?php else: ?>
                <div class="w-16 h-12 bg-[#F8F8F8] text-ink/40 rounded-2xl flex items-center justify-center text-xs font-bold border border-black/5">No Img</div>
              <?php endif; ?>
            </td>

            <td class="py-4 font-display font-black text-sm text-[#18181B] leading-snug">
              <?php echo htmlspecialchars($item['title']); ?>
            </td>

            <td class="py-4">
              <p class="text-xs text-ink/65 line-clamp-2 leading-relaxed max-w-sm font-normal">
                <?php echo htmlspecialchars($item['description']); ?>
              </p>
            </td>

            <td class="py-4 whitespace-nowrap">
              <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#F8F8F8] border border-black/5 text-xs font-bold text-ink">
                <i class="fa-regular fa-calendar text-brand text-xs"></i>
                <span><?php echo date('d/m/Y', strtotime($item['published_date'])); ?></span>
              </span>
            </td>

            <td class="py-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-2">
                <a href="news-edit.php?id=<?php echo $item['id']; ?>" class="inline-flex items-center gap-1.5 bg-[#18181B] hover:bg-black text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm transition-all hover:scale-105">
                  <i class="fa-solid fa-pen-to-square text-xs"></i>
                  <span>Modifica</span>
                </a>

                <button type="button" onclick="confirmDeleteNews(<?php echo $item['id']; ?>)" class="w-9 h-9 rounded-full bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center transition-colors shadow-sm" title="Elimina">
                  <i class="fa-solid fa-trash text-xs"></i>
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

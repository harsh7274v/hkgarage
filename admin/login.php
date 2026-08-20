<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verifyCSRFToken($csrf)) {
        $error = 'Sessione scaduta o Token CSRF non valido. Riprova.';
    } else {
        $result = loginAdmin($email, $password);
        if ($result['success']) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | HK Garage</title>
  
  <!-- Google Fonts & FontAwesome -->
  <!-- Google Fonts: Inter & Montserrat -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #ECECEC; color: #18181B; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="max-w-md w-full bg-white rounded-[32px] shadow-xl p-8 border border-black/5">
    
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-[#18181B] text-white rounded-2xl text-xl font-black font-display mb-3 shadow-md">
        HK
      </div>
      <h1 class="text-2xl font-black font-display tracking-tight text-[#18181B]">HK Garage Admin</h1>
      <p class="text-xs text-black/50 font-semibold uppercase tracking-wider mt-1">Area Riservata di Gestione</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-6 bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-700 text-xs font-bold flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="space-y-5">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div>
        <label class="block text-xs font-bold text-black/60 uppercase tracking-wider mb-2 font-display">Email Amministratore</label>
        <div class="relative">
          <input type="email" name="email" required placeholder="admin@hkgarage.it" class="w-full px-4 py-3.5 bg-[#F8F8F8] border border-black/10 rounded-2xl focus:outline-none focus:border-[#18181B] text-sm font-semibold pl-11">
          <i class="fa-solid fa-envelope absolute left-4 top-4 text-black/40 text-sm"></i>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-black/60 uppercase tracking-wider mb-2 font-display">Password</label>
        <div class="relative">
          <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3.5 bg-[#F8F8F8] border border-black/10 rounded-2xl focus:outline-none focus:border-[#18181B] text-sm font-semibold pl-11">
          <i class="fa-solid fa-key absolute left-4 top-4 text-black/40 text-sm"></i>
        </div>
      </div>

      <button type="submit" class="w-full bg-[#18181B] hover:bg-black text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.01] active:scale-95 text-sm flex items-center justify-center gap-2">
        <span>Accedi al Pannello</span>
        <i class="fa-solid fa-arrow-right text-xs"></i>
      </button>
    </form>

    <div class="mt-8 pt-6 border-t border-black/5 text-center">
      <a href="../index.php" class="text-xs text-black/60 font-bold hover:text-[#18181B] transition-colors inline-flex items-center gap-1.5">
        <i class="fa-solid fa-arrow-left text-[10px]"></i>
        <span>Torna al Sito Pubblico HK Garage</span>
      </a>
    </div>

  </div>
</body>
</html>

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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Montserrat', sans-serif; background-color: #1a1c1e; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-12">
  <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8 border-t-4 border-[#d32f2f]">
    
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 text-[#d32f2f] rounded-full text-2xl font-black mb-3">
        <i class="fa-solid fa-user-shield"></i>
      </div>
      <h1 class="text-2xl font-black uppercase text-[#1a1c1e]">HK Garage Admin</h1>
      <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mt-1">Area Riservata di Gestione</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-6 bg-red-50 border-l-4 border-[#d32f2f] p-4 rounded text-red-700 text-xs font-bold flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-base flex-shrink-0"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="space-y-6">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

      <div>
        <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Email Amministratore</label>
        <div class="relative">
          <input type="email" name="email" required placeholder="admin@hkgarage.it" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#d32f2f] focus:bg-white text-sm font-semibold pl-10">
          <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-gray-400"></i>
        </div>
      </div>

      <div>
        <label class="block text-xs font-extrabold text-gray-700 uppercase mb-2">Password</label>
        <div class="relative">
          <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#d32f2f] focus:bg-white text-sm font-semibold pl-10">
          <i class="fa-solid fa-key absolute left-3.5 top-3.5 text-gray-400"></i>
        </div>
      </div>

      <button type="submit" class="w-full bg-[#d32f2f] hover:bg-[#b71c1c] text-white font-extrabold uppercase py-3.5 rounded-lg shadow-lg transition-transform hover:-translate-y-0.5 text-sm flex items-center justify-center gap-2">
        <i class="fa-solid fa-right-to-bracket"></i> Accedi al Pannello
      </button>
    </form>

    <div class="mt-8 pt-6 border-t text-center">
      <a href="../index.php" class="text-xs text-gray-500 font-bold hover:text-[#d32f2f] transition-colors">
        &larr; Torna al Sito Pubblico HK Garage
      </a>
    </div>

  </div>
</body>
</html>

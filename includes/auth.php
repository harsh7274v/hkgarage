<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

/**
 * Check if an admin is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require login on admin pages. Redirects to login page if unauthenticated.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $currentUrl = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header("Location: /admin/login.php?redirect=" . $currentUrl);
        exit;
    }
}

/**
 * Authenticate admin by email and password
 */
function loginAdmin(string $email, string $password): array {
    $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Inserisci email e password.'];
    }

    try {
        $db = getDBConnection();
        $stmt = $db->prepare("SELECT id, name, email, password FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Prevent session fixation
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['last_activity'] = time();

            return ['success' => true, 'message' => 'Login effettuato con successo.'];
        }

        return ['success' => false, 'message' => 'Credenziali non valide. Riprova.'];
    } catch (Exception $e) {
        error_log("Login Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Errore DB: ' . $e->getMessage()];
    }
}

/**
 * Logout admin
 */
function logoutAdmin(): void {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * CSRF Protection
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(?string $token): bool {
    if (empty($token)) {
        return false;
    }
    if (empty($_SESSION['csrf_token'])) {
        return true;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

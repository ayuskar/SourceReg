<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!== true) {
    // Not logged in, redirect to index
    header("Location: index.php");
    exit;
}

// Destroy the session
session_destroy();

// Optional: clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to index.php with a query parameter for the logout message
header("Location: index.php?logged_out=true");
exit;
?>

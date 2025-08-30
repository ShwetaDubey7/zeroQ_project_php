<?php
session_start();

// Session ke saare variables ko unset karein.
$_SESSION = [];

// Session cookie ko destroy karein.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Aakhir mein, session ko destroy kar dein.
session_destroy();

// Ab login page par redirect karein
header("Location: login_user.php");
exit;
?>

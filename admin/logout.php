<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Optionally, clear cookies related to the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: ../website/login.php?message=successfully_logged_out"); 
exit();
?>

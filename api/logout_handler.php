<?php
// api/logout_handler.php - High-Grade Termination Protocol
include '../includes/connection.php';

// 1. CLEAR SESSION BUFFER
$_SESSION = array();

// 2. COOKIE NEUTRALIZATION
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. FINAL CORE DESTRUCTION
session_destroy();

/** * 4. REDIRECTION HANDSHAKE
 * We send an 'action=logged_out' flag so the login.php can 
 * trigger a "TERMINATION_COMPLETE" notification.
 */
header("Location: ../login.php?action=logout_success");
exit;
?>

<?php
// api/send_dm.php - DIAGNOSTIC MODE
// This will catch the crash and show us the real error
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    // 1. CHECK FILE PATH
    $path = '../includes/connection.php';
    if (!file_exists($path)) {
        throw new Exception("CRITICAL: connection.php not found at: " . realpath('../includes/'));
    }

    // 2. INCLUDE CONNECTION
    require_once $path;

    // 3. CHECK SESSION
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("SESSION ERROR: User not logged in.");
    }

    // 4. CHECK DATABASE VARIABLE
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("DB ERROR: \$conn variable is missing or invalid.");
    }

    // 5. PROCESS REQUEST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sender = $_SESSION['user_id'];
        $receiver = isset($_POST['target_id']) ? intval($_POST['target_id']) : 0;
        $msg = isset($_POST['message']) ? $_POST['message'] : '';

        if (empty($msg) || $receiver === 0) {
            throw new Exception("DATA ERROR: Missing message or target ID.");
        }

        // Safe Escape
        $safeMsg = $conn->real_escape_string($msg);

        // SQL Query
        $sql = "INSERT INTO direct_messages (sender_id, receiver_id, message) 
                VALUES ($sender, $receiver, '$safeMsg')";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            throw new Exception("SQL ERROR: " . $conn->error);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
    }

} catch (Throwable $e) {
    // Return the crash error as JSON so the dashboard can read it
    http_response_code(200); // Force 200 OK so JS reads the error message
    echo json_encode([
        'status' => 'error', 
        'message' => 'SERVER CRASH: ' . $e->getMessage(),
        'line' => $e->getLine()
    ]);
}
?>
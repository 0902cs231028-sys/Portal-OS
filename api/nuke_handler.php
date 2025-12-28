<?php
// api/nuke_handler.php - Architect Termination Protocol
include '../includes/connection.php';

header('Content-Type: application/json');

// Architect Identity Check
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    echo json_encode(['status' => 'error', 'message' => 'UNAUTHORIZED_ACCESS']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_msg') {
    $msg_id = intval($_POST['msg_id']);
    
    $stmt = $conn->prepare("DELETE FROM global_chat WHERE msg_id = ?");
    $stmt->bind_param("i", $msg_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'TERMINATION_FAILED']);
    }
    exit;
}
?>

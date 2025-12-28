<?php
include '../includes/connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { die(json_encode(['status' => 'unauthorized'])); }

$uid = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// 1. Send Connection Offer
if ($action === 'send_offer') {
    $to = mysqli_real_escape_string($conn, $_POST['to_user']);
    $sdp = mysqli_real_escape_string($conn, $_POST['sdp']);
    
    // Clear old signals to prevent ghost calls
    mysqli_query($conn, "DELETE FROM call_signals WHERE to_user = '$to' OR from_user = '$uid'");
    
    $query = "INSERT INTO call_signals (from_user, to_user, type, payload) VALUES ('$uid', '$to', 'offer', '$sdp')";
    mysqli_query($conn, $query);
    echo json_encode(['status' => 'offered']);
}

// 2. Check for Incoming Handshakes
if ($action === 'check_signals') {
    $check = mysqli_query($conn, "SELECT c.*, s.full_name FROM call_signals c 
                                  JOIN students s ON c.from_user = s.student_id 
                                  WHERE c.to_user = '$uid' ORDER BY c.created_at DESC LIMIT 1");
    
    if ($row = mysqli_fetch_assoc($check)) {
        echo json_encode([
            'hasSignal' => true,
            'from_id' => $row['from_user'],
            'from_name' => $row['full_name'],
            'type' => $row['type'],
            'sdp' => $row['payload']
        ]);
    } else {
        echo json_encode(['hasSignal' => false]);
    }
}

// 3. Send Answer back to Caller
if ($action === 'send_answer') {
    $to = mysqli_real_escape_string($conn, $_POST['to_user']);
    $sdp = mysqli_real_escape_string($conn, $_POST['sdp']);
    
    mysqli_query($conn, "UPDATE call_signals SET type = 'answer', payload = '$sdp' WHERE from_user = '$to' AND to_user = '$uid'");
    echo json_encode(['status' => 'answered']);
}
?>

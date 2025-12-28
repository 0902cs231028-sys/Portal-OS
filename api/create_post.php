<?php
// api/create_post.php - Sovereign Broadcast Uplink
include '../includes/connection.php';

header('Content-Type: application/json');

// 1. GATEKEEPER: Identity Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $email = $_SESSION['email'];
    
    // Sanitize transmission payload
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    if (empty(trim($content))) {
        echo json_encode(['status' => 'error', 'message' => 'EMPTY_SIGNAL_BLOCKED']);
        exit;
    }

    // 2. APPROVAL LOGIC: Architect vs Subject
    // Architect (You) bypasses the queue; all others are set to 0 (Pending)
    $is_approved = ($email === 'shiroonigami23@gmail.com') ? 1 : 0;

    // 3. DATABASE INJECTION: Calibrated for Master Intel
    // Column changed from user_id to student_id to match the students table
    $query = "INSERT INTO campus_posts (student_id, content, is_approved, created_at) 
              VALUES ('$uid', '$content', $is_approved, NOW())";
    
    if (mysqli_query($conn, $query)) {
        $msg = ($is_approved) ? 'Broadcast live on Global Network.' : 'Signal queued for Architect approval.';
        echo json_encode(['status' => 'success', 'message' => $msg]);
    } else {
        // Log SQL error for the Architect's eyes
        echo json_encode(['status' => 'error', 'message' => 'UPLINK_CRASH: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'UNAUTHORIZED_ACCESS']);
}
?>

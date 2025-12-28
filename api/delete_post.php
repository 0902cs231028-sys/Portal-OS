<?php
// api/delete_post.php - Architect Termination Protocol
include '../includes/connection.php';

header('Content-Type: application/json');

// Security Gate: Verify Architect Identity
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    echo json_encode(['status' => 'error', 'message' => 'UNAUTHORIZED_ARCHITECT']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $pid = intval($_POST['post_id']);

    // Logic: Permanent removal from the campus_posts matrix
    $query = "DELETE FROM campus_posts WHERE post_id = $pid";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}
?>

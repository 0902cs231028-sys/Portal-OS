<?php
include '../includes/connection.php';

// Strict Admin Gate
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    die(json_encode(['status' => 'error', 'message' => 'AUTH_FAILURE']));
}

$post_id = mysqli_real_escape_string($conn, $_POST['post_id']);
$action = $_POST['action'];

if ($action === 'approve') {
    $sql = "UPDATE campus_posts SET is_approved = 1 WHERE post_id = '$post_id'";
} else {
    $sql = "DELETE FROM campus_posts WHERE post_id = '$post_id'";
}

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>

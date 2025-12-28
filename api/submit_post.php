<?php
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    // Logic: Admin posts are auto-approved, others are pending
    $status = ($_SESSION['email'] === 'shiroonigami23@gmail.com') ? 1 : 0;

    $query = "INSERT INTO campus_posts (user_id, content, is_approved) 
              VALUES ('$uid', '$content', '$status')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Post sent for Admin approval.']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>

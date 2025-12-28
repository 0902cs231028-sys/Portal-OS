<?php
    header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Prevent empty broadcasts
    if (trim($message) === '') {
        echo json_encode(['status' => 'error']);
        exit;
    }

    // Identify if the sender is the Master Admin
    $is_announcement = ($_SESSION['email'] === 'shiroonigami23@gmail.com') ? 1 : 0;

    $query = "INSERT INTO global_chat (sender_id, message, is_announcement) 
              VALUES ('$uid', '$message', '$is_announcement')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]);
    }
}
?>

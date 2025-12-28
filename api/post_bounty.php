<?php
// api/post_bounty.php - Intelligence Request Uplink
include '../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);

    if (empty($title) || empty($desc)) {
        echo json_encode(['status' => 'error', 'message' => 'EMPTY_SIGNAL_PAYLOAD']);
        exit;
    }

    // Alignment: Using table/columns from your existing get_bounties.php
    $query = "INSERT INTO resource_bounties (student_id, request_title, request_description, status) 
              VALUES ('$uid', '$title', '$desc', 'open')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}
?>

<?php
include '../includes/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['hasNew' => false]);
    exit;
}

$uid = $_SESSION['user_id'];

// Check for unread notifications specific to user or global (user_id = 0)
$query = "SELECT * FROM notifications 
          WHERE (user_id = '$uid' OR user_id = 0) 
          AND is_read = 0 
          ORDER BY created_at DESC LIMIT 1";

$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    // Mark as read immediately to avoid double-notifying
    $nid = $row['notif_id'];
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE notif_id = '$nid'");

    echo json_encode([
        'hasNew' => true,
        'title' => $row['title'],
        'message' => $row['message'],
        'link' => $row['link']
    ]);
} else {
    echo json_encode(['hasNew' => false]);
}
?>

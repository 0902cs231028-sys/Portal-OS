<?php
// api/approve_bounty.php - Architect Authorization Protocol
include '../includes/connection.php';

header('Content-Type: application/json');

if ($_SESSION['email'] === 'shiroonigami23@gmail.com' && isset($_POST['bounty_id'])) {
    $bid = intval($_POST['bounty_id']);
    
    // Authorization Logic
    $query = "UPDATE resource_bounties SET is_approved = 1 WHERE bounty_id = $bid";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}
?>

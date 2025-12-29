<?php
// api/delete_bounty.php
include '../includes/connection.php';
header('Content-Type: application/json');

// Architect Identity Verification
if (isset($_SESSION['email']) && $_SESSION['email'] === 'shiroonigami23@gmail.com' && isset($_POST['bounty_id'])) {
    $bid = intval($_POST['bounty_id']);
    
    $query = "DELETE FROM resource_bounties WHERE bounty_id = $bid";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'AUTH_FAILURE']);
}
?>

<?php
// api/fulfill_bounty.php - Resolve Intelligence Request
include '../includes/connection.php';

if ($_SESSION['email'] === 'shiroonigami23@gmail.com' && isset($_POST['bounty_id'])) {
    $bid = intval($_POST['bounty_id']);
    mysqli_query($conn, "UPDATE resource_bounties SET status = 'fulfilled' WHERE bounty_id = $bid");
    echo json_encode(['status' => 'success']);
}
?>

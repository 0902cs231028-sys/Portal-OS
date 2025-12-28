<?php
// api/delete_vault.php
include '../includes/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['vid'])) { exit; }

$vid = intval($_GET['vid']);
$uid = $_SESSION['user_id'];

// 1. Get File Path first
$query = mysqli_query($conn, "SELECT google_drive_link FROM vault_items WHERE vault_id = '$vid' AND student_id = '$uid'");
$file = mysqli_fetch_assoc($query);

if ($file) {
    // 2. Delete Physical File from Server
    $filePath = '../' . $file['google_drive_link'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // 3. Delete Database Record
    mysqli_query($conn, "DELETE FROM vault_items WHERE vault_id = '$vid' AND student_id = '$uid'");
}
?>
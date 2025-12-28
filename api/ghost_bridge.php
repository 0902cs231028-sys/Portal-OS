<?php
include '../includes/connection.php';

// Architect Key Security
$secret_key = "YOUR_HIDDEN_ARCHITECT_KEY"; // Change this
$provided_key = $_SERVER['HTTP_X_BRIDGE_KEY'] ?? '';

if ($provided_key !== $secret_key) {
    die(json_encode(['status' => 'access_denied']));
}

$payload = json_decode(file_get_contents('php://input'), true);

foreach ($payload['files'] as $file) {
    $title = mysqli_real_escape_string($conn, $file['title']);
    $url = mysqli_real_escape_string($conn, $file['source_url']);
    $branch = mysqli_real_escape_string($conn, $file['branch']);
    
    // Check if already exists to prevent duplicates
    $check = mysqli_query($conn, "SELECT id FROM campus_resources WHERE source_url = '$url'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO campus_resources (resource_title, source_url, target_branch) VALUES ('$title', '$url', '$branch')");
    }
}

echo json_encode(['status' => 'bridge_sync_complete']);
?>

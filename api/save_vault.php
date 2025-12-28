<?php
// api/save_vault.php - GITHUB CLOUD STORAGE (PRIVATE VAULT)
header('Content-Type: application/json');
include '../includes/connection.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_id'])) { die(json_encode(['status' => 'error', 'message' => 'Unauthorized'])); }

$uid = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$type = mysqli_real_escape_string($conn, $_POST['type']);

// 2. CONFIGURATION (Digital Vault Repo)
$vault_owner = 'shiroonigami23-ui';
$vault_repo = 'Digital-Vault';
$token = GITHUB_PAT; // From connection.php

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    
    // Prepare File
    $fileContent = file_get_contents($_FILES['file']['tmp_name']);
    $base64Content = base64_encode($fileContent);
    
    $fileInfo = pathinfo($_FILES['file']['name']);
    $ext = strtolower($fileInfo['extension']);
    
    // 3. CREATE UNIQUE PATH: student_id/timestamp_filename
    // This automatically creates the folder for the student if it doesn't exist
    $cleanFileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileInfo['filename']);
    $githubPath = $uid . "/" . time() . "_" . $cleanFileName . "." . $ext;
    
    // 4. GITHUB API PUSH
    $url = "https://api.github.com/repos/$vault_owner/$vault_repo/contents/$githubPath";
    
    $data = json_encode([
        "message" => "Secure Vault Upload: User $uid",
        "content" => $base64Content
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Free hosting fix
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: PortalOS-Vault",
        "Authorization: token $token",
        "Content-Type: application/json"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 5. DATABASE SYNC
    if ($httpCode === 201 || $httpCode === 200) {
        // We save the PATH, not the full URL, for security
        $query = "INSERT INTO vault_items (student_id, doc_title, doc_type, google_drive_link) 
                  VALUES ('$uid', '$title', '$type', '$githubPath')";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database Sync Failed']);
        }
    } else {
        $err = json_decode($response, true);
        echo json_encode(['status' => 'error', 'message' => 'GitHub Rejection: ' . ($err['message'] ?? 'Unknown Error')]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'No file received.']);
}
?>
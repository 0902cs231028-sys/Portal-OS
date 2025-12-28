<?php
// api/view_vault.php - SECURE PROXY VIEWER
include '../includes/connection.php';

// 1. SECURITY GATES
if (!isset($_SESSION['user_id'])) { exit("ACCESS_DENIED"); }
if (!isset($_GET['path'])) { exit("INVALID_REQUEST"); }

$uid = $_SESSION['user_id'];
$path = mysqli_real_escape_string($conn, $_GET['path']);

// 2. OWNERSHIP VERIFICATION (The Critical Step)
// We check if this specific path belongs to the logged-in user in the database.
$check = mysqli_query($conn, "SELECT * FROM vault_items WHERE google_drive_link = '$path' AND student_id = '$uid'");

if (mysqli_num_rows($check) === 0) {
    // If not found, or if it belongs to someone else -> BLOCK IT
    header("HTTP/1.1 403 Forbidden");
    exit("SECURITY_ALERT: UNAUTHORIZED_ACCESS_ATTEMPT_LOGGED");
}

// 3. FETCH FROM PRIVATE REPO
$vault_owner = 'shiroonigami23-ui';
$vault_repo = 'Digital-Vault';
$token = GITHUB_PAT;

$url = "https://api.github.com/repos/$vault_owner/$vault_repo/contents/$path";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: PortalOS-Vault",
    "Authorization: token $token"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    exit("FILE_NOT_FOUND_ON_SERVER");
}

$data = json_decode($response, true);
$downloadUrl = $data['download_url'];

// 4. STREAM CONTENT TO BROWSER
// We fetch the raw content and pass it to the user without exposing the URL
$ch_file = curl_init();
curl_setopt($ch_file, CURLOPT_URL, $downloadUrl);
curl_setopt($ch_file, CURLOPT_RETURNTRANSFER, 0); // Output directly
curl_setopt($ch_file, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch_file, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch_file, CURLOPT_HTTPHEADER, [
    "User-Agent: PortalOS-Vault",
    "Authorization: token $token"
]);

// Determine Content Type
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime_types = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];
header("Content-Type: " . ($mime_types[$ext] ?? 'application/octet-stream'));

curl_exec($ch_file);
curl_close($ch_file);
?>
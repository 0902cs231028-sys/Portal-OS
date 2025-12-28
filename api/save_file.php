<?php
include '../includes/connection.php';

// Architect Gate
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    echo json_encode(['status' => 'error', 'msg' => 'UNAUTHORIZED']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_POST['file'];
    $content = $_POST['content'];
    
    // On InfinityFree, we target the htdocs folder directly
    $baseDir = $_SERVER['DOCUMENT_ROOT']; 
    $fullPath = $baseDir . '/' . ltrim($file, '/');

    // Security: Only allow editing files, not directories
    if (is_dir($fullPath)) {
        echo json_encode(['status' => 'error', 'msg' => 'CANNOT_OVERWRITE_DIRECTORY']);
        exit;
    }

    // Deploy Changes
    if (file_put_contents($fullPath, $content) !== false) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'DISK_WRITE_FAILED']);
    }
}
?>

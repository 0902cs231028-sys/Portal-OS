<?php
include '../includes/connection.php';

// Absolute Architect Gate
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') { 
    die("UNAUTHORIZED_ACCESS"); 
}

$file = $_GET['file'] ?? '';
// Path for InfinityFree htdocs
$fullPath = realpath("../../htdocs/" . $file);

// Hard-block includes and stay within htdocs perimeter
if (!$fullPath || strpos($fullPath, 'htdocs') === false || strpos($file, 'includes') !== false) {
    die("SECURITY_ERR: CORE_VAULT_PROTECTED");
}

if (is_dir($fullPath)) {
    // If it is a directory, generate a virtual listing
    $items = array_diff(scandir($fullPath), array('.', '..', '.git', 'node_modules'));
    $list = [];
    foreach ($items as $item) {
        $list[] = [
            'name' => $item,
            'type' => is_dir($fullPath . '/' . $item) ? 'dir' : 'file',
            'path' => ($file ? $file . '/' : '') . $item
        ];
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'directory', 'items' => $list]);
} else {
    // Stream the raw source code for the editor
    header('Content-Type: text/plain');
    echo file_get_contents($fullPath);
}
?>

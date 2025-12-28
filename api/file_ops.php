<?php
// api/file_ops.php - The Architect's Hammer
session_start();

// 1. Security Gate
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    echo json_encode(['status' => 'error', 'message' => 'ACCESS_DENIED']);
    exit;
}

// 2. Setup
$base_dir = '../'; // Root of htdocs
$action = $_POST['action'] ?? '';
$name = $_POST['name'] ?? '';
$old_name = $_POST['old_name'] ?? '';

// Basic Sanitization
$name = basename($name); 
$old_name = basename($old_name);

// Helper: Recursive Delete for Folders
function recursiveDelete($target) {
    if (is_dir($target)) {
        $files = glob($target . '*', GLOB_MARK); // Mark adds slash to directories
        foreach ($files as $file) {
            recursiveDelete($file);
        }
        rmdir($target);
    } elseif (is_file($target)) {
        unlink($target);
    }
}

// 3. Execution
try {
    if ($action === 'create_file') {
        $path = $base_dir . $name;
        if (file_exists($path)) throw new Exception("File already exists.");
        if (file_put_contents($path, "") === false) throw new Exception("Permission denied.");
        
    } elseif ($action === 'create_folder') {
        $path = $base_dir . $name;
        if (file_exists($path)) throw new Exception("Folder already exists.");
        if (!mkdir($path, 0755)) throw new Exception("Could not create folder.");
        
    } elseif ($action === 'rename_node') {
        $old_path = $base_dir . $old_name;
        $new_path = $base_dir . $name;
        
        if (!file_exists($old_path)) throw new Exception("Original file missing.");
        if (file_exists($new_path)) throw new Exception("Target name already exists.");
        if (!rename($old_path, $new_path)) throw new Exception("Rename failed.");

    } elseif ($action === 'delete_node') {
        // NEW DELETION LOGIC
        $path = $base_dir . $name;
        if (!file_exists($path)) throw new Exception("Target not found.");
        
        // Safety: Prevent deleting critical system folders if needed, e.g.:
        if ($name === 'includes' || $name === 'api') throw new Exception("System Core Protected.");
        
        recursiveDelete($path);

    } else {
        throw new Exception("Unknown Command");
    }

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
<?php
// api/auth_handler.php - Aetheris Core Authentication Engine
include '../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // 1. HARD GATE: Domain Validation
    if (empty($email) || (strpos($email, '@rjit.ac.in') === false && $email !== 'shiroonigami23@gmail.com')) {
        echo json_encode(['status' => 'error', 'message' => 'SECURITY_ERR: CLASSIFIED_DOMAIN_ONLY']);
        exit;
    }

    // 2. IDENTITY EXTRACTION: Parsing the RJIT Matrix
    // Structure: 0902[branch][year][roll]@rjit.ac.in
    $isArchitect = ($email === 'shiroonigami23@gmail.com');
    
    if (!$isArchitect) {
        $prefix = explode('@', $email)[0];
        // Example: 0902cs231045 -> Branch: CS, Year: 23
        $branchCode = substr($prefix, 4, 2); 
        $yearCode = substr($prefix, 6, 2);
        
        $branchMap = ['cs' => 'CSE', 'it' => 'IT', 'ec' => 'ECE', 'me' => 'MECH', 'ce' => 'CIVIL'];
        $branch = isset($branchMap[strtolower($branchCode)]) ? $branchMap[strtolower($branchCode)] : 'GEN';
    }

    // 3. DATABASE SYNCHRONIZATION: Find or Initialize Subject
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // 4. SECURITY CHECK: Banned Subject Protocol
        if ($user['is_banned'] == 1) {
            echo json_encode(['status' => 'error', 'message' => 'ACCESS_TERMINATED: SUBJECT_BLACKLISTED']);
            exit;
        }
        
        $user_id = $user['student_id'];
    } else {
        // 5. AUTO-PROVISIONING: Create entry for new subject
        $default_name = ucwords(explode('@', $email)[0]);
        $ins = $conn->prepare("INSERT INTO students (email, full_name, branch, batch_year) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $email, $default_name, $branch, $yearCode);
        $ins->execute();
        $user_id = $ins->insert_id;
    }

    // 6. SESSION SERIALIZATION
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $isArchitect ? 'admin' : 'student';
    $_SESSION['branch'] = $isArchitect ? 'ARCHITECT' : $branch;

    // 7. RESPONSE: Redirect to Authorized Workspace
    $redirect = $isArchitect ? 'admin/admin_dashboard.php' : 'dashboard.php';
    echo json_encode(['status' => 'success', 'redirect' => $redirect]);
    exit;
}

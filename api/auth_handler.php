<?php
// api/auth_handler.php - The Aetheris Core Gatekeeper
include '../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // 1. HARD GATE: Domain Validation
    if (empty($email) || (strpos($email, '@rjit.ac.in') === false && $email !== 'shiroonigami23@gmail.com')) {
        echo json_encode(['status' => 'error', 'message' => 'SECURITY_ERR: CLASSIFIED_DOMAIN_ONLY']);
        exit;
    }

    // 2. IDENTITY EXTRACTION: Smart Parsing Logic
    $isArchitect = ($email === 'shiroonigami23@gmail.com');
    
    // Default values
    $branch = "GEN";
    $batch_year = date('y'); 
    $roll_no = explode('@', $email)[0];

    // Intelligent Extraction for RJIT Pattern (e.g., 0902cs231028)
    if (strpos($email, '@rjit.ac.in') !== false) {
        $identifier = strtolower($roll_no);
        
        // Extract Branch (finds the letters between numbers)
        if (preg_match('/[a-z]+/', $identifier, $branch_matches)) {
            $branch = strtoupper($branch_matches[0]);
        }

        // Extract Year (finds the 2 digits immediately following the branch)
        // This solves the 202023 error by ensuring we only store "23"
        if (preg_match('/[a-z]+(\d{2})/', $identifier, $year_matches)) {
            $batch_year = $year_matches[1];
        }
    }

    // 3. DATABASE SYNCHRONIZATION
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if ($user['is_banned'] == 1) {
            echo json_encode(['status' => 'error', 'message' => 'ACCESS_TERMINATED: SUBJECT_BLACKLISTED']);
            exit;
        }
        $user_id = $user['student_id'];
    } else {
        // AUTO-PROVISIONING with Smart Details
        $default_name = ucwords(str_replace('.', ' ', explode('@', $email)[0]));
        
        // We now use the $branch and $batch_year we parsed above
        $ins = $conn->prepare("INSERT INTO students (email, full_name, branch, batch_year, roll_no) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("sssss", $email, $default_name, $branch, $batch_year, $roll_no);
        $ins->execute();
        $user_id = $ins->insert_id;
    }

    // 4. SESSION SERIALIZATION
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $isArchitect ? 'admin' : 'student';

    $redirect = $isArchitect ? 'admin/admin_dashboard.php' : 'dashboard.php';
    echo json_encode(['status' => 'success', 'redirect' => $redirect]);
    exit;
}

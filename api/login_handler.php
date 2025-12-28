<?php
// api/login_handler.php - The Aetheris Core Gatekeeper
include '../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email = mysqli_real_escape_string($conn, $email);

    // 1. HARD GATE: Domain Validation
    if (empty($email) || (strpos($email, '@rjit.ac.in') === false && $email !== 'shiroonigami23@gmail.com')) {
        echo json_encode(['status' => 'error', 'message' => 'SECURITY_ERR: CLASSIFIED_DOMAIN_ONLY']);
        exit;
    }

    // 2. CHECK EXISTING SUBJECT
    $check = mysqli_query($conn, "SELECT * FROM students WHERE email = '$email'");
    $user = mysqli_fetch_assoc($check);

    if ($user) {
        // SECURITY: Check if Architect has Nuked this subject
        if ($user['is_banned'] == 1) {
            echo json_encode(['status' => 'error', 'message' => 'TERMINATED: Access revoked by Architect.']);
            exit;
        }

        // SESSION SERIALIZATION
        $_SESSION['user_id'] = $user['student_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = ($email === 'shiroonigami23@gmail.com') ? 'admin' : 'student';
        
        echo json_encode(['status' => 'success', 'role' => $_SESSION['role']]);
        exit;
    } else {
        /**
         * 3. AUTO-PROVISIONING: Smart Identity Extraction
         * Calibrated for RJIT Pattern (e.g., 0902cs231028)
         */
        $roll_no = explode('@', $email)[0];
        $identifier = strtolower($roll_no);
        $branch = "GEN";
        $batch_year = date('y'); // Default to current 2-digit year

        // Extract Branch (e.g., 'cs') and Year (e.g., '23')
        if (preg_match('/([a-z]+)(\d{2})/', $identifier, $matches)) {
            $branch = strtoupper($matches[1]);
            $batch_year = $matches[2]; // Captures '23', killing the 202023 error
        }

        $default_name = ucwords(str_replace('.', ' ', $roll_no));

        // INJECT NEW NODE INTO MATRIX
        $ins_query = "INSERT INTO students (email, full_name, roll_no, branch, batch_year) 
                      VALUES ('$email', '$default_name', '$roll_no', '$branch', '$batch_year')";
        
        if (mysqli_query($conn, $ins_query)) {
            $new_id = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $new_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'student';
            
            echo json_encode(['status' => 'success', 'message' => 'New Identity Initialized.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'IDENTITY_PROVISIONING_FAILED']);
        }
    }
}

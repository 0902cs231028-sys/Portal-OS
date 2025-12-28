<?php
/**
 * AETHERIS CORE v2.0 - Central Intelligence Connection
 * Role: System Heartbeat & Security Logic
 */

// 1. ARCHITECT OVERRIDE: Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. CRYPTOGRAPHIC CREDENTIALS
define('DB_HOST', 'locally');
define('DB_USER', 'you');
define('DB_PASS', 'hide all time');
define('DB_NAME', 'whatever u like');

// 3. APEX RECON KEYS (GitHub Intelligence)
define('GITHUB_PAT', 'my fav thingy');
define('REPO_OWNER', 'u should make own');
define('REPO_NAME', 'life-solution');

// 4. ESTABLISH CORE CONNECTION
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    // Return 503 Service Unavailable if DB is down (Better than text die)
    http_response_code(503);
    die("<pre style='color:#ef4444; background:#020617; padding:20px;'>SYSTEM_ERR: DATABASE_HANDSHAKE_FAILED</pre>");
}

mysqli_set_charset($conn, "utf8mb4");

// 5. SESSION HARDENING & INITIALIZATION
// FIXED: Only start session if one doesn't exist to prevent Locking/502 Errors
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    session_start();
}

// 6. HELPER FUNCTIONS
function getStudentDetails($email) {
    if (preg_match('/0902([a-z]+)(\d{2})(\d+)/i', $email, $matches)) {
        $branchCode = strtolower($matches[1]);
        $branchMap = ['cs' => 'CSE', 'it' => 'IT', 'ec' => 'ECE', 'me' => 'MECH', 'ce' => 'CIVIL'];
        return [
            'branch' => isset($branchMap[$branchCode]) ? $branchMap[$branchCode] : strtoupper($branchCode),
            'year'   => '20' . $matches[2],
            'roll'   => $matches[3]
        ];
    }
    return ['branch' => 'UNKN', 'year' => 'UNKN', 'roll' => '0000'];
}

function scrub($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// 7. DYNAMIC SEMESTER ENGINE
function getSemesterProgress() {
    $m = (int)date('n');
    $d = (int)date('j');
    $y = (int)date('Y');
    $ts = time();

    // CYCLE 1: EVEN SEM (Feb 15 - July 15)
    if ($m >= 2 && $m <= 7) {
        $startDate = strtotime("$y-02-15");
        $endDate = strtotime("$y-07-15");
    } 
    // CYCLE 2: ODD SEM (Aug 20 - Jan 30)
    else {
        // Handle Year Wrap-around (Jan belongs to previous year's semester)
        $startYear = ($m == 1) ? $y - 1 : $y;
        $endYear = ($m == 1) ? $y : $y + 1;
        
        $startDate = strtotime("$startYear-08-20");
        $endDate = strtotime("$endYear-01-30");
    }

    // Logic: Gap Months
    if ($ts < $startDate && $m == 8) return 0; // Pre-Start (Aug 1-19)
    if ($ts > $endDate && $m == 7) return 100; // Post-End (July 16-31)

    // Calculate Percentage
    $totalSeconds = $endDate - $startDate;
    $elapsedSeconds = $ts - $startDate;
    
    // Prevent Division by Zero
    if ($totalSeconds <= 0) return 0;

    $percent = round(($elapsedSeconds / $totalSeconds) * 100);
    return max(0, min(100, $percent));
}
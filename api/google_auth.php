<?php
// api/google_auth.php - Google Identity Exchange
include '../includes/connection.php';

// Architect Configuration (Must match Google Cloud Console)
$clientID = "i dont know";
$clientSecret = "lol";
$redirectUri = "https://shiroonigami23.free.nf/api/google_auth.php";

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. EXCHANGE CODE FOR ACCESS TOKEN
    $url = 'https://oauth2.googleapis.com/token';
    $params = [
        'code' => $code,
        'client_id' => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        // 2. FETCH USER PROFILE DATA
        $idToken = $tokenData['id_token'];
        $profileUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
        $profileResponse = file_get_contents($profileUrl);
        $profile = json_decode($profileResponse, true);

        $email = $profile['email'];
        $name = $profile['name'];
        $picture = $profile['picture'];

        // 3. ARCHITECT DOMAIN VALIDATION
        if (strpos($email, '@rjit.ac.in') === false && $email !== 'shiroonigami23@gmail.com') {
            die("SECURITY_ERR: CLASSIFIED_DOMAIN_ONLY");
        }

        // 4. SYNC WITH DATABASE
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            // Check for Banned Status
            if ($user['is_banned'] == 1) { die("ACCESS_TERMINATED"); }
            $user_id = $user['student_id'];
        } else {
            // New Subject Auto-Provisioning
            $details = getStudentDetails($email);
            $ins = $conn->prepare("INSERT INTO students (email, full_name, branch, batch_year, profile_pic) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("sssss", $email, $name, $details['branch'], $details['year'], $picture);
            $ins->execute();
            $user_id = $ins->insert_id;
        }

        // 5. ESTABLISH SESSION
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = ($email === 'shiroonigami23@gmail.com') ? 'admin' : 'student';

        header("Location: ../dashboard.php");
        exit;
    }
}
header("Location: ../login.php?error=auth_failed");
exit;
?>

<?php
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_SESSION['user_id'];
    $summary = mysqli_real_escape_string($conn, $_POST['summary']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $base64_image = $_POST['profile_img_base64'];

    // 1. Update Resume Table
    $check = mysqli_query($conn, "SELECT resume_id FROM student_resume_data WHERE student_id = '$uid'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE student_resume_data SET professional_summary = '$summary', skills_json = '$skills' WHERE student_id = '$uid'");
    } else {
        mysqli_query($conn, "INSERT INTO student_resume_data (student_id, professional_summary, skills_json) VALUES ('$uid', '$summary', '$skills')");
    }

    // 2. Handle Image Saving (Bypassing Upload Limits)
    if (!empty($base64_image)) {
        $img_data = str_replace('data:image/jpeg;base64,', '', $base64_image);
        $img_data = str_replace(' ', '+', $img_data);
        $data = base64_decode($img_data);
        
        $file_name = "profile_" . $uid . "_" . time() . ".jpg";
        $file_path = "../assets/uploads/" . $file_name;
        $db_path = "assets/uploads/" . $file_name;

        if (file_put_contents($file_path, $data)) {
            mysqli_query($conn, "UPDATE students SET profile_pic = '$db_path' WHERE student_id = '$uid'");
        }
    }

    echo json_encode(['status' => 'success']);
}
?>

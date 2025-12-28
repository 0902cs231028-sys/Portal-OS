<?php
include '../includes/connection.php';

// Strict Architect-Only Gate
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    die(json_encode(['status' => 'error', 'message' => 'SYSTEM_ERR: UNAUTHORIZED_ACCESS_LOGGED']));
}

header('Content-Type: application/json');

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // 1. NUKE MESSAGE: Delete specific chat or DM
    if ($action === 'delete_msg' && isset($_POST['msg_id'])) {
        $msg_id = mysqli_real_escape_string($conn, $_POST['msg_id']);
        mysqli_query($conn, "DELETE FROM global_chat WHERE msg_id = '$msg_id'");
        echo json_encode(['status' => 'success', 'op' => 'MSG_ERASED']);
    }

    // 2. TERMINATE USER: Ban a "Black Sheep" student
    if ($action === 'ban_user' && isset($_POST['student_id'])) {
        $sid = mysqli_real_escape_string($conn, $_POST['student_id']);
        // Marks user as banned; your login.php should check this column
        mysqli_query($conn, "UPDATE students SET is_banned = 1 WHERE student_id = '$sid'");
        echo json_encode(['status' => 'success', 'message' => 'SUBJECT_TERMINATED']);
    }

    // 3. NUKE BOUNTY: Wipe irrelevant requests from the Bounty Board
    if ($action === 'nuke_bounty' && isset($_POST['bounty_id'])) {
        $bid = mysqli_real_escape_string($conn, $_POST['bounty_id']);
        mysqli_query($conn, "DELETE FROM resource_bounties WHERE bounty_id = '$bid'");
        echo json_encode(['status' => 'success', 'message' => 'BOUNTY_WIPED']);
    }

    // 4. SCRUB BROADCAST: Delete an approved post from the Wall
    if ($action === 'nuke_post' && isset($_POST['post_id'])) {
        $pid = mysqli_real_escape_string($conn, $_POST['post_id']);
        mysqli_query($conn, "DELETE FROM campus_posts WHERE post_id = '$pid'");
        echo json_encode(['status' => 'success', 'message' => 'POST_SCRUBBED']);
    }
}
?>

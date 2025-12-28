<?php
// api/get_dms.php - Secure Infiltration Feed
include '../includes/connection.php';
if (!isset($_SESSION['user_id'])) exit;

$me = $_SESSION['user_id'];
$target = intval($_GET['target_id']);

$query = "SELECT * FROM direct_messages 
          WHERE (sender_id = $me AND receiver_id = $target) 
          OR (sender_id = $target AND receiver_id = $me) 
          ORDER BY sent_at ASC";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    echo '<div class="text-center text-slate-600 mono text-[9px] mt-20 uppercase tracking-[0.5em]">NO_TRANS_HISTORY</div>';
}

while ($msg = mysqli_fetch_assoc($result)) {
    $isMe = ($msg['sender_id'] == $me);
    echo '
    <div class="flex '.($isMe ? 'justify-end' : 'justify-start').' mb-4">
        <div class="p-4 rounded-2xl text-xs '.($isMe ? 'bg-blue-600/20 border border-blue-500/30 text-blue-100 rounded-tr-none' : 'bg-slate-800 border border-white/5 text-slate-300 rounded-tl-none').'">
            '.htmlspecialchars($msg['message']).'
            <div class="text-[8px] mt-2 opacity-30 mono uppercase">'.date('H:i', strtotime($msg['sent_at'])).'</div>
        </div>
    </div>';
}
?>

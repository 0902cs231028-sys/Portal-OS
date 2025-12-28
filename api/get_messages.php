<?php
// api/get_messages.php - Fixed with Navigation Links
ini_set('display_errors', 0); // Turn off display errors to prevent breaking JSON/HTML flow
header("Access-Control-Allow-Origin: *");
include '../includes/connection.php';

// Check for table existence
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'global_chat'");
if(mysqli_num_rows($tableCheck) == 0) {
    die("<div class='text-center text-red-500 mono text-[10px]'>SYSTEM_ERROR: Table 'global_chat' not found.</div>");
}

$query = "SELECT m.*, s.student_id, s.full_name, s.email, s.profile_pic, s.role 
          FROM global_chat m 
          LEFT JOIN students s ON m.sender_id = s.student_id 
          ORDER BY m.sent_at ASC LIMIT 50";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $current_user = $_SESSION['user_id'] ?? '';
        $isMe = ($row['sender_id'] == $current_user);
        $isAdminMsg = ($row['is_announcement'] == 1);
        
        // Name Fallback
        $displayName = $row['full_name'] ?: 'Unknown User';
        if($displayName == 'New Student' && !empty($row['email'])) {
            $displayName = ucwords(explode('@', $row['email'])[0]);
        }
        
        $p_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'assets/profile.png';
        
        // --- NAVIGATION LINK LOGIC ---
        // This is the magic line. Clicking sends them to dashboard.php?view=THEIR_ID
        $profileLink = "dashboard.php?view=" . $row['student_id'];

        // Styling Variables
        $align = $isMe ? 'flex-row-reverse' : 'flex-row';
        $textAlign = $isMe ? 'items-end' : 'items-start';
        $bubbleStyle = $isAdminMsg 
            ? 'bg-blue-900/20 border-blue-500/30 text-blue-100 shadow-[0_0_15px_rgba(59,130,246,0.2)]' 
            : ($isMe ? 'bg-blue-600/20 border-blue-500/30 text-white' : 'bg-slate-800/40 border-white/5 text-slate-300');

        echo '
        <div class="group flex items-start gap-4 mb-6 '.$align.' animate-in fade-in slide-in-from-bottom-2 duration-300">
            
            <a href="'.$profileLink.'" class="shrink-0 relative hover:scale-105 transition-transform cursor-pointer">
                <img src="'.$p_pic.'" class="w-10 h-10 rounded-xl border border-white/10 object-cover shadow-lg">
            </a>
            
            <div class="flex flex-col '.$textAlign.' max-w-[80%]">
                <div class="flex items-center mb-1.5 gap-2 '.($isMe ? 'flex-row-reverse' : 'flex-row').'">
                    <a href="'.$profileLink.'" class="text-[10px] font-black uppercase tracking-widest hover:text-blue-400 transition-colors cursor-pointer '.($isAdminMsg ? 'text-blue-400' : 'text-slate-400').'">
                        '.$displayName.'
                    </a>
                    '.($row['role'] === 'admin' ? '<span class="px-1.5 py-0.5 bg-red-600/20 text-red-500 text-[8px] font-bold rounded uppercase">ARCHITECT</span>' : '').'
                </div>
                
                <div class="relative p-4 rounded-2xl backdrop-blur-sm border '.$bubbleStyle.'">
                    <p class="text-sm font-medium leading-relaxed">'.nl2br(htmlspecialchars($row['message'])).'</p>
                </div>
                
                <span class="text-[9px] text-slate-600 font-mono mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    '.date('H:i', strtotime($row['sent_at'])).'
                </span>
            </div>
        </div>';
    }
} else {
    echo "<div class='text-center opacity-20 mono text-[10px] mt-20 uppercase tracking-[0.5em]'>Frequency_Clear: No_Signals</div>";
}
?>
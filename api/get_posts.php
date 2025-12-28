<?php
// api/get_posts.php - Supreme Global Feed Engine
include '../includes/connection.php';

// Architect Check
$isAdmin = (isset($_SESSION['email']) && $_SESSION['email'] === 'shiroonigami23@gmail.com');

// FIX: Joining on student_id instead of user_id to match Master Intel
$query = "SELECT p.*, s.full_name, s.profile_pic, s.branch, s.email, s.roll_no, s.student_id 
          FROM campus_posts p 
          JOIN students s ON p.student_id = s.student_id 
          WHERE p.is_approved = 1 
          ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    echo '<div class="col-span-full text-center py-20 opacity-20 mono text-xs uppercase tracking-[0.5em]">No_Global_Transmissions_Detected</div>';
}

while ($row = mysqli_fetch_assoc($result)) {
    $isArchitect = ($row['email'] === 'shiroonigami23@gmail.com');
    $p_pic = (!empty($row['profile_pic'])) ? $row['profile_pic'] : 'assets/profile.png';
    $roll = (!empty($row['roll_no'])) ? strtoupper($row['roll_no']) : 'ID_UNVERIFIED';
    
    echo '
    <div class="masonry-item">
        <div class="post-card p-6 rounded-[2.5rem] '.($isArchitect ? 'border-blue-500/30 bg-blue-500/5' : '').'">
            <div class="flex items-center gap-4 mb-6">
                <div class="relative">
                    <img src="'.$p_pic.'" class="w-12 h-12 rounded-2xl object-cover border '.($isArchitect ? 'border-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.3)]' : 'border-white/10').'">
                    '.($isArchitect ? '<div class="absolute -top-1 -right-1 bg-blue-500 p-1 rounded-full"><i data-lucide="shield-check" class="w-2 h-2 text-white"></i></div>' : '').'
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-black uppercase tracking-tight '.($isArchitect ? 'text-blue-400' : 'text-white').'">
                        '.$row['full_name'].'
                    </h4>
                    <p class="text-[8px] text-slate-500 font-mono tracking-widest uppercase">
                        '.$row['branch'].' UNIT // ROLL: '.$roll.'
                    </p>
                </div>
            </div>

            <p class="text-slate-300 text-sm leading-relaxed mb-8 font-light italic">
                '.nl2br(htmlspecialchars($row['content'])).'
            </p>

            <div class="flex items-center justify-between pt-6 border-t border-white/5">
                <div class="flex gap-4">
                    <button class="flex items-center gap-2 text-slate-600 hover:text-blue-500 transition-all group">
                        <i data-lucide="heart" class="w-4 h-4 group-hover:fill-blue-500"></i>
                        <span class="text-[9px] font-black">24</span>
                    </button>
                    <button class="flex items-center gap-2 text-slate-600 hover:text-purple-500 transition-all">
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        <span class="text-[9px] font-black">12</span>
                    </button>
                </div>
                <div class="flex gap-2">
                    <button onclick="openDM('.$row['student_id'].', \''.$row['full_name'].'\', \''.$p_pic.'\')" 
                            class="p-2 bg-white/5 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                    '.($isAdmin ? '<button class="p-2 bg-red-500/10 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button>' : '').'
                </div>
            </div>
            <div class="mt-4 text-[7px] text-slate-700 font-mono uppercase tracking-[0.3em]">
                Timestamp: '.date('Y.m.d // H:i:s', strtotime($row['created_at'])).'
            </div>
        </div>
    </div>';
}
?>

<?php
// api/search_students.php - Architect's Subject Locator
include '../includes/connection.php';

// Gatekeeper: Architect Clearance Only
if ($_SESSION['role'] !== 'admin') { exit; }

$query = mysqli_real_escape_string($conn, $_GET['query']);

// Search logic for Name or Roll No
$sql = "SELECT student_id, full_name, roll_no, email, branch, is_banned 
        FROM students 
        WHERE full_name LIKE '%$query%' OR roll_no LIKE '%$query%' 
        LIMIT 5";

$result = mysqli_query($conn, $sql);
$output = '';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status = ($row['is_banned'] == 1) ? 'BANNED' : 'ACTIVE';
        $statusColor = ($row['is_banned'] == 1) ? 'text-red-500' : 'text-emerald-500';
        
        $output .= '
        <div class="p-4 bg-white/5 border border-white/10 rounded-2xl flex justify-between items-center mb-2 hover:bg-white/10 transition-all">
            <div>
                <h4 class="text-white font-bold text-sm">'.$row['full_name'].'</h4>
                <p class="text-[9px] text-slate-500 font-mono">'.$row['roll_no'].' | '.$row['branch'].'</p>
            </div>
            <div class="text-right">
                <span class="text-[8px] font-black '.$statusColor.'">'.$status.'</span>
                <div class="flex gap-2 mt-2">
                    <button onclick="viewProfile('.$row['student_id'].')" class="text-blue-500 hover:text-white transition-all"><i data-lucide="eye" class="w-4 h-4"></i></button>
                    <button onclick="toggleBan('.$row['student_id'].')" class="text-red-500 hover:text-white transition-all"><i data-lucide="slash" class="w-4 h-4"></i></button>
                </div>
            </div>
        </div>';
    }
} else {
    $output = '<div class="text-center py-6 text-slate-600 font-mono text-[10px]">NO_SUBJECTS_LOCATED</div>';
}

echo $output;
?>

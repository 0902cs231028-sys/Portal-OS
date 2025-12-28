<?php
include '../includes/connection.php';
$uid = $_SESSION['user_id'];
$isArchitect = ($_SESSION['email'] === 'shiroonigami23@gmail.com');

$query = "SELECT b.*, s.full_name FROM resource_bounties b 
          JOIN students s ON b.student_id = s.student_id 
          ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Dynamic Status Styling
        $isOpen = ($row['status'] === 'open');
        $statusClass = $isOpen ? 'status-open' : 'status-fulfilled';
        $statusIcon = $isOpen ? 'loader-2' : 'check-circle-2';
        $animate = $isOpen ? 'animate-spin' : '';
        
        echo '
        <div id="bounty-'.$row['bounty_id'].'" class="bounty-card p-8 rounded-[2.5rem] flex flex-col justify-between group relative overflow-hidden transition-all duration-500 hover:shadow-[0_0_40px_rgba(59,130,246,0.15)] hover:border-blue-500/30">
            
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none group-hover:bg-blue-500/10 transition-all"></div>

            <div>
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border '.$statusClass.'">
                        <i data-lucide="'.$statusIcon.'" class="w-3 h-3 '.$animate.'"></i>
                        '.$row['status'].'
                    </div>
                    <p class="text-[9px] text-slate-600 font-mono tracking-widest">ID_'.sprintf("%04d", $row['bounty_id']).'</p>
                </div>
                
                <h4 class="text-xl font-black text-white mb-3 tracking-tight group-hover:text-blue-400 transition-colors">'.htmlspecialchars($row['request_title']).'</h4>
                <p class="text-xs text-slate-400 leading-relaxed font-medium">'.nl2br(htmlspecialchars($row['request_description'])).'</p>
            </div>
            
            <div class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-slate-900 rounded-full border border-white/5 text-slate-500 group-hover:text-blue-500 transition-colors">
                        <i data-lucide="user" class="w-3 h-3"></i>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Requester</p>
                        <p class="text-[10px] text-white font-bold tracking-wide">'.$row['full_name'].'</p>
                    </div>
                </div>

                '.($isArchitect && $isOpen ? '
                    <button onclick="fulfillBounty('.$row['bounty_id'].')" 
                            class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all flex items-center gap-2 shadow-lg hover:shadow-emerald-900/20">
                        <i data-lucide="check" class="w-3 h-3"></i> Resolve
                    </button>
                ' : '').'
            </div>
        </div>';
    }
} else {
    echo '<div class="col-span-full text-center py-20 opacity-30 flex flex-col items-center">
            <i data-lucide="ghost" class="w-12 h-12 mb-4 text-slate-500"></i>
            <p class="font-mono text-xs uppercase tracking-[0.3em] text-slate-500">No_Active_Contracts</p>
          </div>';
}
?>
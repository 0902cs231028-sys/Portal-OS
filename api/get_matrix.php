<?php
// api/get_matrix.php - Sovereign Matrix Bridge
include '../includes/connection.php';

if (!isset($_GET['id'])) { die("TARGET_NODE_UNDEFINED"); }
$id = intval($_GET['id']);

// Fetch actual GitHub assets from the vault
$query = "SELECT * FROM vault_items WHERE student_id = $id ORDER BY vault_id DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="space-y-4 p-2">';
    while ($row = mysqli_fetch_assoc($result)) {
        $fileUrl = $row['google_drive_link'];
        $fileName = $row['doc_title'];
        
        echo '
        <div class="group flex items-center justify-between p-5 bg-white/[0.02] border border-white/5 rounded-3xl hover:border-blue-500/30 transition-all">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-500">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm">'.htmlspecialchars($fileName).'</h4>
                    <span class="text-[9px] text-slate-500 mono uppercase tracking-widest">Type: '.$row['doc_type'].'</span>
                </div>
            </div>
            <button onclick="openMatrix(\''.$fileUrl.'\', \''.$fileName.'\')" 
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black rounded-xl transition-all shadow-lg shadow-blue-900/20 uppercase tracking-widest">
                Initiate_Read
            </button>
        </div>';
    }
    echo '</div>';
} else {
    echo '<div class="text-center py-20 opacity-20 mono text-[10px] uppercase tracking-[0.5em]">No_Vault_Assets_Indexed</div>';
}
?>

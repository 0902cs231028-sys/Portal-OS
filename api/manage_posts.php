<?php
include '../includes/connection.php';
// Master Gate: Architect clearance only
if ($_SESSION['role'] !== 'admin') { die("ACCESS_DENIED: UNAUTHORIZED_NODE"); }

// Corrected JOIN logic using student_id and pulling Roll Number
$query = "SELECT p.*, s.full_name, s.roll_no, s.branch 
          FROM campus_posts p 
          JOIN students s ON p.student_id = s.student_id 
          WHERE p.is_approved = 0 
          ORDER BY p.created_at DESC";
$pending = mysqli_query($conn, $query);
?>

<div class="p-6">
    <div class="flex items-center gap-4 mb-8">
        <div class="w-2 h-8 bg-yellow-500 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black uppercase tracking-tighter italic">Pending<span class="text-yellow-500">_Broadcasts</span></h2>
            <p class="text-[10px] text-slate-500 font-mono tracking-[0.3em]">SIGNAL_QUEUE: <?php echo mysqli_num_rows($pending); ?> DETECTED</p>
        </div>
    </div>

    <div class="grid gap-6">
        <?php if(mysqli_num_rows($pending) === 0): ?>
            <div class="py-20 text-center glass-card rounded-[2.5rem] border-dashed border-white/5">
                <p class="text-slate-600 font-mono text-[10px] uppercase tracking-[0.5em]">Network_Clear: No_Pending_Signals</p>
            </div>
        <?php endif; ?>

        <?php while($post = mysqli_fetch_assoc($pending)): ?>
        <div id="post-<?php echo $post['post_id']; ?>" class="glass-card p-8 rounded-[2.5rem] border-l-4 border-yellow-500/50 hover:border-yellow-500 transition-all group">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center border border-white/5 group-hover:bg-yellow-500/10 transition-all">
                        <i data-lucide="radio" class="w-5 h-5 text-yellow-500"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black text-xs uppercase tracking-widest"><?php echo $post['full_name']; ?></h4>
                        <p class="text-[9px] text-slate-500 font-mono italic">
                            UNIT: <?php echo $post['branch']; ?> | ROLL: <?php echo $post['roll_no'] ?: 'N/A'; ?>
                        </p>
                    </div>
                </div>
                <span class="text-[8px] text-slate-600 font-mono uppercase tracking-widest"><?php echo date('H:i | d.m.y', strtotime($post['created_at'])); ?></span>
            </div>

            <div class="bg-black/20 p-6 rounded-2xl border border-white/5 mb-6">
                <p class="text-slate-300 text-sm leading-relaxed italic">
                    "<?php echo nl2br(htmlspecialchars($post['content'])); ?>"
                </p>
            </div>

            <div class="flex gap-4">
                <button onclick="processBroadcast(<?php echo $post['post_id']; ?>, 'approve')" 
                        class="flex-1 py-4 bg-emerald-600/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-lg shadow-emerald-900/10">
                    Authorize_Signal
                </button>
                <button onclick="processBroadcast(<?php echo $post['post_id']; ?>, 'delete')" 
                        class="flex-1 py-4 bg-red-600/10 border border-red-500/20 text-red-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-lg shadow-red-900/10">
                    Terminate_Node
                </button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    /**
     * PROCESS_BROADCAST: Handles signal authorization or termination
     */
    async function processBroadcast(id, action) {
        const endpoint = action === 'approve' ? '../api/approve_post.php' : '../api/delete_post.php';
        
        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${id}`
            });
            const data = await res.json();

            if (data.status === 'success') {
                gsap.to(`#post-${id}`, {
                    opacity: 0,
                    x: 100,
                    duration: 0.5,
                    onComplete: () => document.getElementById(`post-${id}`).remove()
                });
                notify('PROTOCOL_EXECUTED', `Broadcast successfully ${action}d.`, 'success');
            }
        } catch (err) {
            notify('UPLINK_ERR', 'Critical failure during authorization.', 'error');
        }
    }
    
    lucide.createIcons();
</script>

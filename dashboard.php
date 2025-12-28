<?php
include 'includes/connection.php';

// 1. HARD SECURITY: Gatekeeper
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$user_email = $_SESSION['email'];

// 2. VIEW LOGIC
$view_id = isset($_GET['view']) ? mysqli_real_escape_string($conn, $_GET['view']) : $uid;
$is_viewing_others = ($view_id != $uid);

// 3. LOGGING
if ($is_viewing_others) {
    mysqli_query($conn, "INSERT INTO profile_visits (visitor_id, profile_owner_id) VALUES ('$uid', '$view_id')");
}

// 4. FETCH DATA
$profile_query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$view_id'");
$p_data = mysqli_fetch_assoc($profile_query);
if (!$p_data) { header("Location: dashboard.php"); exit; }

$display_name = ($p_data['full_name'] == 'New Student' || empty($p_data['full_name'])) 
                ? ucwords(explode('@', $p_data['email'])[0]) 
                : $p_data['full_name'];

$resume_query = mysqli_query($conn, "SELECT * FROM student_resume_data WHERE student_id = '$view_id'");
$resume = mysqli_fetch_assoc($resume_query);

// 5. CALL THE NEW SEMESTER ENGINE
$percentage = getSemesterProgress();

// Determine Active Phase Visualization
$active_stage = 'unit'; 
if ($percentage > 45) $active_stage = 'lab';
if ($percentage > 85) $active_stage = 'final';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_viewing_others ? "$display_name | Profile" : "Command Center"; ?> | PortalOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background: #0b1120; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        .glass-card { background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); }
        .stat-card { background: linear-gradient(145deg, #1e293b, #0f172a); border: 1px solid rgba(59, 130, 246, 0.1); }
        .stage-active { box-shadow: 0 0 20px rgba(59, 130, 246, 0.6); border-color: #3b82f6; transform: scale(1.1); }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
    </style>
</head>
<body class="flex min-h-screen overflow-hidden">

    <aside class="w-72 sidebar-gradient border-r border-slate-800 hidden lg:flex flex-col">
        <div class="p-8">
            <h2 class="text-3xl font-black text-blue-500 tracking-tighter italic">Portal<span class="text-white">OS</span></h2>
            <p class="text-[9px] text-slate-500 font-mono mt-1 tracking-[0.2em] uppercase">Security_Protocol_Active</p>
        </div>
        
        <nav class="flex-1 px-6 space-y-3">
            <a href="dashboard.php" class="flex items-center p-4 <?php echo !$is_viewing_others ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'text-slate-400 hover:bg-slate-800'; ?> rounded-2xl transition-all font-bold group">
                <i data-lucide="layout-dashboard" class="mr-3 group-hover:scale-110 transition-transform"></i> 
                <?php echo $is_viewing_others ? "Back to Mine" : "Command Center"; ?>
            </a>

            <?php if (!$is_viewing_others): ?>
                <a href="message.php" class="flex items-center p-4 text-slate-400 hover:bg-slate-800 rounded-2xl transition-all group">
                    <i data-lucide="globe" class="mr-3 group-hover:text-blue-500 transition-colors"></i> Global Network
                </a>
                <a href="vault.php" class="flex items-center p-4 text-slate-400 hover:bg-slate-800 rounded-2xl transition-all group">
                    <i data-lucide="shield-check" class="mr-3 group-hover:text-blue-500 transition-colors"></i> Achievement Vault
                </a>
                <a href="bounty_board.php" class="flex items-center p-4 text-slate-400 hover:bg-slate-800 rounded-2xl transition-all group">
                    <i data-lucide="target" class="mr-3 group-hover:text-blue-500 transition-colors"></i> Bounty Board
                </a>
                <a href="profile.php" class="flex items-center p-4 text-slate-400 hover:bg-slate-800 rounded-2xl transition-all group">
                    <i data-lucide="user-cog" class="mr-3 group-hover:text-blue-500 transition-colors"></i> Professional Identity
                </a>
            <?php endif; ?>
        </nav>

        <?php if($user_email === 'shiroonigami23@gmail.com'): ?>
            <div class="p-6 mt-auto border-t border-slate-800">
                <a href="admin/admin_dashboard.php" class="flex items-center p-4 bg-red-500/10 text-red-500 font-black rounded-2xl hover:bg-red-600 hover:text-white transition-all border border-red-500/20">
                    <i data-lucide="zap" class="mr-3"></i> HYPER-CONTROL
                </a>
            </div>
        <?php endif; ?>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        
        <header class="h-24 border-b border-slate-800 flex items-center justify-between px-10 bg-[#0b1120]/95 backdrop-blur-xl z-20">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    <?php echo $is_viewing_others ? "Infiltrating: <span class='text-blue-500'>$display_name</span>" : "System Status: <span class='text-blue-500 uppercase'>Operational</span>"; ?>
                </h1>
            </div>
            
            <div class="flex items-center gap-6">
                <button onclick="requestNotificationAccess()" class="p-2 bg-slate-800 rounded-full hover:bg-blue-600 transition-colors group" title="Enable Neural Link">
                    <i data-lucide="bell" class="w-5 h-5 text-slate-400 group-hover:text-white"></i>
                </button>

                <div class="text-right hidden sm:block">
                    <p class="text-[10px] text-blue-500 font-black uppercase tracking-widest"><?php echo $p_data['branch']; ?> UNIT</p>
                    <p class="text-sm font-medium text-slate-400 italic">20<?php echo $p_data['batch_year']; ?>_PROTOCOL</p>
                </div>
                <div class="relative group">
                    <img src="<?php echo $p_data['profile_pic']; ?>" class="w-12 h-12 rounded-2xl border-2 border-blue-500/20 object-cover shadow-xl group-hover:border-blue-500 transition-all" onerror="this.src='assets/profile.png'">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-4 border-[#0b1120] rounded-full"></div>
                </div>
            </div>
        </header>

        <section class="p-10 overflow-y-auto custom-scrollbar flex-1">
            
            <?php if ($is_viewing_others): ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 glass-card p-10 rounded-[3rem]">
                        <h3 class="text-blue-500 font-black uppercase tracking-[0.3em] text-[10px] mb-8 flex items-center gap-2">
                            <span class="w-8 h-[1px] bg-blue-500/30"></span> Professional Brief
                        </h3>
                        <p class="text-xl leading-relaxed text-slate-300 font-light italic">
                            "<?php echo $resume['professional_summary'] ?? 'Subject has not yet initialized an executive brief.'; ?>"
                        </p>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="glass-card p-10 rounded-[3rem] text-center border-blue-500/20 bg-blue-500/5">
                            <div class="flex flex-col gap-3">
                                <button onclick="initiateCall(<?php echo $view_id; ?>)" class="w-full py-5 bg-blue-600 rounded-3xl font-black hover:bg-blue-500 transition-all flex items-center justify-center gap-3">
                                    <i data-lucide="phone-call"></i> P2P VOICE
                                </button>
                                <button onclick="openDM(<?php echo $view_id; ?>, '<?php echo $display_name; ?>', '<?php echo $p_data['profile_pic']; ?>')" 
                                        class="w-full py-5 bg-slate-900 border border-white/10 rounded-3xl font-black hover:bg-slate-800 transition-all flex items-center justify-center gap-3">
                                    <i data-lucide="message-square-more"></i> PRIVATE DM
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="md:col-span-3 glass-card p-10 rounded-[3rem] mb-4 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-10 opacity-10 group-hover:opacity-20 transition-opacity"><i data-lucide="route" class="w-32 h-32 text-blue-500"></i></div>
                        
                        <div class="flex justify-between items-center mb-10 relative z-10">
                            <div>
                                <h3 class="text-xl font-black uppercase italic tracking-widest text-white">The_Nexus</h3>
                                <p class="text-[10px] text-slate-500 font-mono mt-1">SEMESTER_PROGRESS_TRACKER // LIVE_SYNC</p>
                            </div>
                            <div class="text-right">
                                <span class="text-3xl font-black text-blue-500"><?php echo $percentage; ?>%</span>
                                <p class="text-[9px] text-slate-500 uppercase font-bold">Term_Completion</p>
                            </div>
                        </div>

                        <div class="relative flex items-center justify-between px-4 pb-4">
                            <div class="absolute h-1 bg-slate-800 left-0 right-0 z-0"></div>
                            <div class="absolute h-1 bg-blue-600 left-0 z-0 shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: <?php echo $percentage; ?>%;"></div>
                            
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-4 border-[#0b1120] flex items-center justify-center transition-all <?php echo ($active_stage == 'unit') ? 'bg-blue-500 text-white stage-active' : (($percentage > 20) ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-500'); ?>">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </div>
                                <p class="text-[9px] font-black text-slate-500 mt-3 uppercase <?php echo ($active_stage == 'unit') ? 'text-blue-400' : ''; ?>">Unit-Tests</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-4 border-[#0b1120] flex items-center justify-center transition-all <?php echo ($active_stage == 'lab') ? 'bg-blue-500 text-white stage-active animate-pulse' : (($percentage > 50) ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-500'); ?>">
                                    <i data-lucide="pen-tool" class="w-4 h-4"></i>
                                </div>
                                <p class="text-[9px] font-black text-slate-500 mt-3 uppercase <?php echo ($active_stage == 'lab') ? 'text-blue-400' : ''; ?>">Lab_Submissions</p>
                            </div>

                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full border-4 border-[#0b1120] flex items-center justify-center transition-all <?php echo ($active_stage == 'final') ? 'bg-blue-500 text-white stage-active' : (($percentage > 90) ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-500'); ?>">
                                    <i data-lucide="flag" class="w-4 h-4"></i>
                                </div>
                                <p class="text-[9px] font-black text-slate-500 mt-3 uppercase <?php echo ($active_stage == 'final') ? 'text-blue-400' : ''; ?>">Final_Exams</p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card p-8 rounded-[3rem]">
                        <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Network Exposure</h3>
                        <?php 
                        $visits = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM profile_visits WHERE profile_owner_id = '$uid'"));
                        ?>
                        <p class="text-5xl font-black mt-2"><?php echo sprintf("%02d", $visits['total']); ?></p>
                    </div>

                    <div class="md:col-span-2 glass-card p-8 rounded-[3rem]">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-black uppercase italic tracking-widest text-white">
                                <i data-lucide="layers" class="inline mr-2 text-blue-500"></i> Apex_Matrix
                            </h3>
                            <div class="flex gap-2">
                                <button onclick="openMatrixModal(<?php echo $view_id; ?>)" class="text-[9px] text-blue-500 border border-blue-500/30 px-3 py-1 rounded-full hover:bg-blue-500 hover:text-white transition-all">
                                    MY_VAULT
                                </button>
                                <button onclick="openGitHubModal()" class="text-[9px] text-emerald-500 border border-emerald-500/30 px-3 py-1 rounded-full hover:bg-emerald-500 hover:text-white transition-all">
                                    CLASS_SOLUTIONS
                                </button>
                            </div>
                        </div>
                        <div id="matrixListContainer" class="space-y-4">
                            <div class="text-center py-6 opacity-20 mono text-[10px] uppercase">Initialize_Scan_To_View_Vault</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </section>
    </main>

    <div id="matrixViewer" class="hidden fixed inset-0 z-[300] bg-slate-950/98 backdrop-blur-3xl flex flex-col">
        <div class="h-20 border-b border-white/5 flex items-center justify-between px-10 bg-white/5">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-600/20 rounded-xl flex items-center justify-center text-blue-500"><i data-lucide="file-text"></i></div>
                <div>
                    <h3 id="matrixFileName" class="font-black text-sm uppercase tracking-widest text-white">Syncing_Solution...</h3>
                    <p class="text-[9px] text-slate-500 font-mono tracking-widest uppercase italic">Secure_Internal_Stream</p>
                </div>
            </div>
            <div class="flex gap-4">
                <a id="matrixDownload" href="#" download class="px-6 py-2 bg-slate-800 hover:bg-slate-700 rounded-xl font-bold text-[10px] flex items-center gap-2 transition-all"><i data-lucide="download" class="w-3 h-3"></i> OFFLINE_CACHE</a>
                <button onclick="closeMatrix()" class="px-6 py-2 bg-red-600/10 text-red-500 border border-red-500/20 rounded-xl font-bold text-[10px] hover:bg-red-600 hover:text-white transition-all">TERMINATE_SESSION</button>
            </div>
        </div>
        <div id="matrixBody" class="p-6 overflow-y-auto max-h-[600px] hidden custom-scrollbar"></div>
        <div class="flex-1 w-full bg-black/20"><iframe id="matrixFrame" class="w-full h-full border-none" src=""></iframe></div>
    </div>

    <div id="activeCallHud" class="hidden fixed top-6 left-1/2 -translate-x-1/2 z-[250] w-auto px-8 py-4 glass-card rounded-full border-blue-500/50 flex items-center gap-6 shadow-[0_0_40px_rgba(59,130,246,0.2)]">
        <div class="flex items-center gap-3 border-r border-white/10 pr-6">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <span id="activeCallTarget" class="text-xs font-black uppercase tracking-widest text-white italic">P2P_LINK_ACTIVE</span>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="toggleMute()" id="muteBtn" class="text-slate-400 hover:text-white transition-colors"><i data-lucide="mic"></i></button>
            <button onclick="endCall()" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-full flex items-center justify-center shadow-lg shadow-red-900/40 transition-all"><i data-lucide="phone-off" class="w-5 h-5 text-white"></i></button>
        </div>
    </div>

    <div id="dmDrawer" class="fixed inset-y-0 right-0 w-full md:w-[450px] bg-[#020617]/95 backdrop-blur-3xl border-l border-white/5 transform translate-x-full transition-transform duration-500 z-[100] shadow-[-20px_0_50px_rgba(0,0,0,0.5)]">
        <div class="flex flex-col h-full">
            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
                <div class="flex items-center gap-4">
                    <img id="dmTargetPic" src="assets/profile.png" class="w-12 h-12 rounded-2xl border-2 border-blue-500 shadow-lg object-cover">
                    <div>
                        <h3 id="dmTargetName" class="text-lg font-black tracking-tighter uppercase italic text-white">Subject_Node</h3>
                        <p id="dmTargetStatus" class="text-[9px] text-emerald-500 font-mono tracking-widest uppercase">Encryption_Active</p>
                    </div>
                </div>
                <button onclick="closeDM()" class="p-3 hover:bg-red-500/10 hover:text-red-500 rounded-2xl transition-all"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <div id="dmFeed" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-900/10 via-transparent to-transparent"></div>
            <div class="p-6 border-t border-white/5">
                <form id="dmForm" class="relative">
                    <input type="hidden" id="dmTargetId">
                    <input type="text" id="dmInput" autocomplete="off" placeholder="Enter encrypted transmission..." class="w-full bg-black/40 border border-white/10 p-5 pr-16 rounded-[2rem] outline-none focus:border-blue-600 transition-all text-sm font-medium">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-500 transition-all shadow-lg shadow-blue-900/40"><i data-lucide="send" class="w-5 h-5"></i></button>
                </form>
            </div>
        </div>
    </div>
    
    <audio id="remoteAudio" autoplay></audio>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simple-peer/9.11.1/simplepeer.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/talkin.js"></script>
    <script src="js/dm_system.js"></script>
    <script src="js/matrix_viewer.js"></script>

    <script>
        lucide.createIcons();
        // --- SERVICE WORKER IGNITION ---
        // This is what links your sw.js to the system!
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(reg => console.log("NEURAL_LINK_ESTABLISHED:", reg.scope))
                .catch(err => console.error("NEURAL_LINK_FAILURE:", err));
        }

        function requestNotificationAccess() {
            Notification.requestPermission().then(perm => {
                if (perm === 'granted') {
                    alert("NEURAL_UPLINK_GRANTED: You will now receive background intelligence.");
                }
            });
        }

        // --- GITHUB & MATRIX LOGIC ---
        async function openGitHubModal() {
            const viewer = document.getElementById('matrixViewer');
            const matrixBody = document.getElementById('matrixBody');
            const matrixFrame = document.getElementById('matrixFrame');
            
            viewer.classList.remove('hidden');
            matrixBody.classList.remove('hidden');
            matrixFrame.classList.add('hidden');
            
            matrixBody.innerHTML = '<div class="text-center py-10 animate-pulse mono text-[10px] text-emerald-500">AUTHENTICATING_GITHUB_PAT...</div>';
            
            try {
                const res = await fetch('api/get_github_files.php');
                const html = await res.text();
                matrixBody.innerHTML = html;
                if (window.lucide) lucide.createIcons();
            } catch (err) {
                matrixBody.innerHTML = '<div class="text-red-500 mono text-[10px] text-center">UPLINK_FAILED</div>';
            }
        }

        async function openMatrixModal(targetId) {
            const viewer = document.getElementById('matrixViewer');
            const matrixBody = document.getElementById('matrixBody');
            const matrixFrame = document.getElementById('matrixFrame');
            
            viewer.classList.remove('hidden');
            matrixBody.classList.remove('hidden');
            matrixFrame.classList.add('hidden'); 
            
            matrixBody.innerHTML = '<div class="text-center py-10 animate-pulse mono text-[10px]">ESTABLISHING_ENCRYPTED_LINK...</div>';
            
            try {
                const res = await fetch(`api/get_matrix.php?id=${targetId}`);
                const data = await res.text();
                matrixBody.innerHTML = data;
                if (window.lucide) { lucide.createIcons(); }
            } catch (err) {
                matrixBody.innerHTML = '<div class="text-red-500 mono text-[10px]">LINK_STABILITY_FAILED</div>';
            }
        }

        function openMatrix(url, name) {
            const matrixBody = document.getElementById('matrixBody');
            const matrixFrame = document.getElementById('matrixFrame');
            
            matrixBody.classList.add('hidden'); 
            matrixFrame.classList.remove('hidden'); 
            
            document.getElementById('matrixFrame').src = url;
            document.getElementById('matrixFileName').innerText = name;
            document.getElementById('matrixDownload').href = url;
        }

        function closeMatrix() {
            document.getElementById('matrixViewer').classList.add('hidden');
            document.getElementById('matrixFrame').src = "";
        }
    </script>
</body>
</html>
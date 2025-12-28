<?php
include '../includes/connection.php';

// Architect Identity Verification
if ($_SESSION['email'] !== 'shiroonigami23@gmail.com') {
    die("ACCESS_DENIED: UNAUTHORIZED_ARCHITECT_IDENTITY");
}

/** * SUPREME HUD CALIBRATION */
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM students"))['c'];
$total_visits = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM profile_visits"))['c'];

// 1. Verification Required
$p_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM campus_posts WHERE is_approved = 0"))['c'] ?? 0;
$b_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM resource_bounties WHERE is_approved = 0"))['c'] ?? 0;
$v_total = $p_pending + $b_pending;

// 2. Matrix Bounties
$open_bounties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM resource_bounties WHERE is_approved = 1"))['c'] ?? 0;

/** * RECURSIVE REGISTRY: Restoring Source Node Scan */
$files = array_diff(scandir('../'), array('.', '..', 'includes', '.git', 'assets', 'node_modules'));
if (!$files) { $files = []; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aetheris Core | Sovereign Command</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Space+Grotesk:wght@300;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #ef4444; --bg: #020617; }
        body { background: var(--bg); color: #f8fafc; font-family: 'Space Grotesk', sans-serif; overflow-x: hidden; selection: background: var(--accent); color: white; }
        .terminal-card { 
            background: rgba(15, 23, 42, 0.4); 
            backdrop-filter: blur(40px); 
            border: 1px solid rgba(255,255,255,0.05); 
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); 
        }
        .terminal-card:hover { border-color: var(--accent); transform: translateY(-5px); box-shadow: 0 20px 60px -20px rgba(239, 68, 68, 0.15); }
        .pulse-red { animation: pulse-red 2.5s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        .shimmer { 
            background: linear-gradient(90deg, transparent 0%, rgba(239, 68, 68, 0.1) 50%, transparent 100%); 
            background-size: 200% 100%; 
            animation: shimmer 3s infinite linear; 
        }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    </style>
</head>
<body class="p-4 md:p-12">

    <div class="max-w-[1900px] mx-auto">
        <header class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-20 gap-10">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-600/10 border border-red-500/20 text-red-500 text-[9px] font-black uppercase tracking-[0.4em] shimmer">
                    Live_Status: God_Mode_Active
                </div>
                <h1 class="text-7xl md:text-9xl font-black tracking-tighter uppercase italic text-white leading-none">
                    Command<span class="text-red-600">_Center</span>
                </h1>
                <p class="text-slate-500 mono text-xs tracking-[0.6em] uppercase">Security_Protocol_v3.0 // Shiro_Onigami</p>
            </div>
            
            <div class="flex flex-wrap gap-4">
                <div class="terminal-card px-8 py-4 rounded-2xl flex items-center gap-4">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-ping"></div>
                    <span class="mono text-[10px] uppercase font-bold tracking-widest">Network_Stability: 99.9%</span>
                </div>
                <a href="../dashboard.php" class="px-10 py-5 bg-white text-black rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all shadow-2xl flex items-center gap-3">
                    <i data-lucide="power"></i> Terminate_Session
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <div class="terminal-card p-10 rounded-[3rem] cursor-pointer hover:bg-blue-600/5 transition-all" onclick="scrollToLogs()">
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.3em] mb-3">Target_Nodes</p>
                <h2 class="text-6xl font-black"><?php echo sprintf("%02d", $total_students); ?></h2>
            </div>

            <div class="terminal-card p-10 rounded-[3rem] border-red-500/20 <?php echo ($v_total > 0) ? 'pulse-red' : ''; ?> cursor-pointer hover:bg-red-600/5" onclick="scrollToQueue()">
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.3em] mb-3">Verification_Required</p>
                <h2 class="text-6xl font-black <?php echo ($v_total > 0) ? 'text-red-600' : 'text-slate-700'; ?>">
                    <?php echo sprintf("%02d", $v_total); ?>
                </h2>
            </div>

            <div class="terminal-card p-10 rounded-[3rem] border-yellow-500/20 cursor-pointer hover:bg-yellow-600/5" onclick="window.location.href='../bounty_board.php'">
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.3em] mb-3">Matrix_Bounties</p>
                <h2 class="text-6xl font-black text-yellow-500"><?php echo sprintf("%02d", $open_bounties); ?></h2>
            </div>
            
            <div class="terminal-card p-10 rounded-[3rem] border-blue-500/10 cursor-pointer hover:bg-blue-600/5">
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.3em] mb-3">Infiltrations</p>
                <h2 class="text-6xl font-black text-blue-500"><?php echo sprintf("%02d", $total_visits); ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-4 space-y-8">
                <div class="terminal-card p-12 rounded-[4rem]">
                    <div class="flex justify-between items-center mb-10">
                        <h3 class="text-xs font-black uppercase tracking-[0.4em] text-red-500 flex items-center gap-4">
                            <i data-lucide="database"></i> SRC_REGISTRY
                        </h3>
                        <span class="text-[9px] mono text-slate-600 uppercase">Includes_Vault_Locked</span>
                    </div>

                    <div class="flex gap-4 mb-8">
                        <button onclick="systemPrompt('create_file')" class="flex-1 py-4 bg-blue-600/10 border border-blue-500/20 rounded-2xl text-[10px] font-black uppercase tracking-widest text-blue-500 hover:bg-blue-600 hover:text-white transition-all shadow-lg flex items-center justify-center gap-2 group">
                            <i data-lucide="plus" class="w-3 h-3 group-hover:rotate-90 transition-transform"></i> New_File
                        </button>
                        <button onclick="systemPrompt('create_folder')" class="flex-1 py-4 bg-emerald-600/10 border border-emerald-500/20 rounded-2xl text-[10px] font-black uppercase tracking-widest text-emerald-500 hover:bg-emerald-600 hover:text-white transition-all shadow-lg flex items-center justify-center gap-2 group">
                            <i data-lucide="folder-plus" class="w-3 h-3 group-hover:scale-110 transition-transform"></i> New_Folder
                        </button>
                    </div>
                    
                    <div id="file_list" class="space-y-4 max-h-[700px] overflow-y-auto pr-4 custom-scrollbar">
                        <?php foreach($files as $file): ?>
                        <div class="group flex justify-between items-center p-6 bg-white/[0.02] border border-white/5 rounded-3xl hover:bg-red-600/5 transition-all cursor-pointer">
                            <div class="flex items-center gap-5 overflow-hidden w-full" onclick="openEditor('<?php echo $file; ?>')">
                                <i data-lucide="file-code" class="w-5 h-5 text-slate-700 group-hover:text-red-500 transition-colors shrink-0"></i>
                                <span class="text-sm mono text-slate-400 group-hover:text-white transition-colors truncate"><?php echo $file; ?></span>
                            </div>
                            
                            <button onclick="deleteNode('<?php echo $file; ?>', event)" class="p-2 text-slate-700 hover:text-red-500 hover:bg-red-500/10 rounded-full transition-all shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="terminal-card p-10 rounded-[3rem] border-blue-500/10 relative">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-blue-500 mb-6 flex items-center gap-3">
                        <i data-lucide="search"></i> Subject_Locator
                    </h3>
                    <input type="text" placeholder="Roll_No / Email / Name..." 
                           class="w-full p-5 rounded-2xl bg-black/40 border border-white/5 outline-none focus:border-blue-600 transition-all mono text-xs uppercase italic text-white">
                    <div id="search_results" class="mt-4 space-y-2 max-h-[300px] overflow-y-auto custom-scrollbar"></div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-12">
                <div id="target_queue" class="terminal-card p-12 rounded-[5rem]">
                    <div class="flex justify-between items-center mb-12">
                        <h3 class="text-xs font-black uppercase tracking-[0.5em] text-emerald-500 flex items-center gap-4">
                            <i data-lucide="radio-tower"></i> BROADCAST_QUEUE
                        </h3>
                        <button onclick="loadQueue()" class="text-slate-600 hover:text-white transition-all"><i data-lucide="refresh-cw"></i></button>
                    </div>
                    <div id="queue_loader" class="space-y-8 min-h-[300px] flex items-center justify-center">
                        <div class="flex flex-col items-center gap-6">
                            <div class="w-16 h-16 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                            <span class="mono text-[10px] text-slate-600 uppercase tracking-[0.6em]">Scanning_Broadcast_Packets...</span>
                        </div>
                    </div>
                </div>

                <div id="target_logs" class="terminal-card p-12 rounded-[5rem] border-purple-500/10">
                    <h3 class="text-xs font-black uppercase tracking-[0.5em] text-purple-500 mb-12 flex items-center gap-4">
                        <i data-lucide="eye"></i> INFILTRATION_LOGS
                    </h3>
                    <div class="overflow-x-auto rounded-[3rem] border border-white/5">
                        <table class="w-full text-left mono text-[12px]">
                            <thead class="bg-white/5 text-slate-500 uppercase">
                                <tr>
                                    <th class="p-8">Origin_Node</th>
                                    <th class="p-8">Target_Infiltration</th>
                                    <th class="p-8 text-right">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php 
                                $v_logs = mysqli_query($conn, "SELECT pv.*, s1.full_name as visitor, s2.full_name as target 
                                                               FROM profile_visits pv 
                                                               JOIN students s1 ON pv.visitor_id = s1.student_id 
                                                               JOIN students s2 ON pv.profile_owner_id = s2.student_id 
                                                               ORDER BY pv.visit_time DESC LIMIT 10");
                                while($log = mysqli_fetch_assoc($v_logs)): ?>
                                <tr class="hover:bg-white/[0.03] transition-colors group">
                                    <td class="p-8 text-slate-300 font-bold tracking-tighter uppercase italic"><?php echo $log['visitor']; ?></td>
                                    <td class="p-8 text-blue-400 group-hover:text-blue-300 transition-colors uppercase italic"><?php echo $log['target']; ?></td>
                                    <td class="p-8 text-right text-slate-600"><?php echo date('H:i:s', strtotime($log['visit_time'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editor_modal" class="hidden fixed inset-0 z-[1000] bg-slate-950/98 backdrop-blur-3xl flex flex-col p-8 md:p-20">
        <div class="flex justify-between items-end mb-12">
            <div>
                <div class="flex items-center gap-6">
                    <h2 id="edit_filename" class="text-5xl font-black text-red-600 mono tracking-tighter leading-none"></h2>
                    <button onclick="initiateRename()" class="p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 hover:border-white/20 transition-all group" title="Rename Node">
                        <i data-lucide="edit-3" class="w-5 h-5 text-slate-400 group-hover:text-white transition-colors"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4 mt-6 text-[11px] mono text-slate-500 uppercase tracking-widest">
                    <span class="w-3 h-3 bg-red-600 rounded-full animate-pulse"></span> SYSTEM_OVERRIDE_ACTIVE
                </div>
            </div>
            <div class="flex gap-6">
                <button onclick="saveFile()" class="px-12 py-5 bg-emerald-600 rounded-3xl font-black text-xs hover:bg-emerald-500 transition-all flex items-center gap-4 shadow-2xl shadow-emerald-900/30">
                    <i data-lucide="check-square"></i> Deactivate_Override_&_Deploy
                </button>
                <button onclick="closeEditor()" class="px-12 py-5 bg-slate-900 rounded-3xl font-black text-xs hover:bg-slate-800 transition-all border border-white/10 uppercase tracking-widest">Abort_Command</button>
            </div>
        </div>
        <textarea id="editor_area" class="flex-1 bg-black/40 border border-white/10 rounded-[4rem] p-16 mono text-sm text-emerald-400 outline-none resize-none custom-scrollbar shadow-[inset_0_0_150px_rgba(0,0,0,0.8)]"></textarea>
    </div>

    <script>
        lucide.createIcons();

        function scrollToQueue() { document.getElementById('target_queue').scrollIntoView({ behavior: 'smooth' }); }
        function scrollToLogs() { document.getElementById('target_logs').scrollIntoView({ behavior: 'smooth' }); }

        // --- SEARCH UPLINK ---
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('input[placeholder="Roll_No / Email / Name..."]');
            const resultsArea = document.getElementById('search_results');
            
            if (searchInput) {
                searchInput.addEventListener('input', async (e) => {
                    const query = e.target.value.trim();
                    if (query.length < 2) { resultsArea.innerHTML = ''; return; }
                    try {
                        const res = await fetch(`../api/search_students.php?query=${encodeURIComponent(query)}`);
                        const html = await res.text();
                        resultsArea.innerHTML = html; 
                        lucide.createIcons();
                    } catch (err) { console.error("SEARCH_FAILURE"); }
                });
            }
        });

        // --- 1. FILE SYSTEM (Enhanced with Delete) ---
        async function openEditor(path) {
            try {
                const res = await fetch(`../api/get_file_content.php?file=${path}`);
                const contentType = res.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    // IT'S A DIRECTORY
                    const data = await res.json();
                    let html = `<div class="p-5 text-red-600 mono text-[10px] border-b border-white/5 mb-8 uppercase tracking-[0.5em] font-black italic">Directory_Node: ${path || 'ROOT'}</div>`;
                    
                    if (path) {
                        const parent = path.split('/').slice(0, -1).join('/');
                        html += `<div class="p-6 bg-white/5 rounded-3xl cursor-pointer hover:bg-red-600/10 mb-4 flex items-center group transition-all" onclick="openEditor('${parent}')"><i data-lucide="arrow-up" class="w-4 h-4 mr-5 text-red-500 group-hover:scale-125 transition-transform"></i><span class="text-sm mono text-red-500 font-bold uppercase tracking-widest">.. [Back_To_Parent]</span></div>`;
                    }

                    data.items.forEach(item => {
                        const icon = item.type === 'dir' ? 'folder' : 'file-code';
                        // ADDED DELETE BUTTON IN DYNAMIC LIST
                        html += `
                        <div class="group flex justify-between items-center p-6 bg-white/[0.02] border border-white/5 rounded-3xl hover:bg-red-600/5 transition-all cursor-pointer">
                            <div class="flex items-center gap-5 overflow-hidden w-full" onclick="openEditor('${item.path}')">
                                <i data-lucide="${icon}" class="w-5 h-5 ${item.type === 'dir' ? 'text-yellow-500' : 'text-slate-500'} group-hover:scale-110 transition-transform"></i>
                                <span class="text-sm mono text-slate-400 group-hover:text-white transition-colors truncate">${item.name}</span>
                            </div>
                            <button onclick="deleteNode('${item.path}', event)" class="p-2 text-slate-700 hover:text-red-500 hover:bg-red-500/10 rounded-full transition-all shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>`;
                    });
                    document.getElementById('file_list').innerHTML = html;
                    lucide.createIcons();
                } else {
                    // IT'S A FILE
                    const content = await res.text();
                    document.getElementById('edit_filename').innerText = path;
                    document.getElementById('editor_area').value = content;
                    document.getElementById('editor_modal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            } catch(e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
        // --- 2. DELETE LOGIC ---
        function deleteNode(name, event) {
            if(event) event.stopPropagation();
            
            Swal.fire({
                title: 'PERMANENT DELETION',
                text: `Destroy ${name}? This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'DESTROY',
                background: '#020617', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    performFileOp('delete_node', name);
                }
            });
        }

        // --- 3. CORE OPS (Fixed JSON parsing) ---
        async function performFileOp(action, name, oldName = null) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('name', name);
            if(oldName) formData.append('old_name', oldName);

            try {
                const res = await fetch('../api/file_ops.php', { method: 'POST', body: formData });
                const text = await res.text();
                
                try {
                    const data = JSON.parse(text);
                    if(data.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Executed', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, background: '#020617', color: '#fff' });
                        if(action === 'rename_node') { closeEditor(); }
                        location.reload(); 
                    } else {
                        throw new Error(data.message);
                    }
                } catch(e) {
                    // Show RAW error if JSON fails
                    Swal.fire({ icon: 'error', title: 'SERVER ERROR', text: text.substring(0, 200), background: '#020617', color: '#ef4444' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'COMMAND FAILED', text: e.message, background: '#020617', color: '#ef4444' });
            }
        }

        async function saveFile() {
            const filename = document.getElementById('edit_filename').innerText;
            const content = document.getElementById('editor_area').value;
            const formData = new FormData();
            formData.append('file', filename);
            formData.append('content', content);

            try {
                const res = await fetch('../api/save_file.php', { method: 'POST', body: formData });
                const text = await res.text();
                
                try {
                    const data = JSON.parse(text);
                    if(data.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Source Deployed', background: '#020617', color: '#fff', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                        closeEditor();
                    } else {
                        throw new Error(data.msg || "Unknown Error");
                    }
                } catch(e) {
                     Swal.fire({ icon: 'error', title: 'DEPLOY ERROR', text: text, background: '#020617', color: '#ef4444' });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'CRITICAL ERROR', text: e.message, background: '#020617', color: '#ef4444' });
            }
        }

        function systemPrompt(action) {
            Swal.fire({
                title: action === 'create_file' ? 'NEW SOURCE NODE' : 'NEW DIRECTORY',
                input: 'text',
                background: '#020617', color: '#fff',
                showCancelButton: true
            }).then((result) => { if (result.isConfirmed) performFileOp(action, result.value); });
        }

        function initiateRename() {
            const currentName = document.getElementById('edit_filename').innerText;
            Swal.fire({
                title: 'RENAME PROTOCOL',
                input: 'text',
                inputValue: currentName,
                background: '#020617', color: '#fff',
                showCancelButton: true
            }).then((result) => { if (result.isConfirmed) performFileOp('rename_node', result.value, currentName); });
        }

        function closeEditor() {
            document.getElementById('editor_modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function loadQueue() {
            fetch('../api/get_posts.php?admin_view=true').then(res => res.text()).then(html => { 
                document.getElementById('queue_loader').innerHTML = html;
                lucide.createIcons();
            });
        }
        loadQueue();
        setInterval(loadQueue, 15000);
    </script>
</body>
</html>
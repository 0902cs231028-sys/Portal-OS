<?php
include 'includes/connection.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bounty Board | PortalOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        body { background: #020617; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); }
        .bounty-card { 
            background: rgba(30, 41, 59, 0.3); 
            border: 1px solid rgba(255,255,255,0.05); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bounty-card:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.2); }
        
        .status-open { color: #facc15; background: rgba(250, 204, 21, 0.1); border: 1px solid rgba(250, 204, 21, 0.2); }
        .status-fulfilled { color: #22c55e; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
    </style>
</head>
<body class="p-6 md:p-12 min-h-screen">

    <div class="max-w-7xl mx-auto">
        
        <header class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <a href="dashboard.php" class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-500 transition-colors mb-4 font-mono text-xs uppercase tracking-widest group">
                    <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i> Return_to_Command
                </a>
                <h1 class="text-6xl font-black tracking-tighter uppercase italic text-white">Bounty<span class="text-blue-500">_Board</span></h1>
                <p class="text-slate-500 font-mono text-[10px] tracking-[0.4em] mt-3 uppercase">Crowdsourced_Intelligence_Protocol</p>
            </div>
            
            <button onclick="openBountyModal()" class="group relative px-8 py-5 bg-blue-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 transition-all shadow-2xl shadow-blue-900/40 flex items-center gap-3 overflow-hidden">
                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                <i data-lucide="plus-circle" class="w-5 h-5 relative z-10"></i> 
                <span class="relative z-10">Request_Intel</span>
            </button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="bounty_container">
            <div class="col-span-full text-center py-20 opacity-30 flex flex-col items-center">
                <i data-lucide="loader-2" class="w-10 h-10 animate-spin mb-4 text-blue-500"></i>
                <p class="font-mono text-xs uppercase tracking-[0.3em]">Syncing_Bounty_Ledger...</p>
            </div>
        </div>
    </div>

    <div id="bountyModal" class="hidden fixed inset-0 z-[200] bg-slate-950/95 backdrop-blur-2xl flex items-center justify-center p-6">
        <div class="glass-card p-12 rounded-[4rem] max-w-xl w-full border border-blue-500/20 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter mb-8 flex items-center gap-3">
                <i data-lucide="target" class="text-blue-500"></i> Request_Intel
            </h2>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 ml-2">Mission Objective</label>
                    <input type="text" id="bTitle" placeholder="e.g. OS Lab Manual Unit 3" class="w-full p-5 rounded-2xl bg-black/40 border border-white/10 outline-none focus:border-blue-600 transition-all text-sm font-medium text-white placeholder:text-slate-600">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 ml-2">Briefing Details</label>
                    <textarea id="bDesc" placeholder="Describe exactly what resource is missing..." class="w-full p-5 rounded-2xl bg-black/40 border border-white/10 h-32 outline-none focus:border-blue-600 transition-all text-sm font-medium text-white placeholder:text-slate-600 resize-none custom-scrollbar"></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="submitBounty()" class="flex-1 py-5 bg-blue-600 rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 transition-all shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
                        <i data-lucide="radio-tower" class="w-4 h-4"></i> Transmit_Signal
                    </button>
                    <button onclick="closeBountyModal()" class="px-10 py-5 bg-slate-900 border border-white/10 rounded-3xl font-black text-xs uppercase tracking-widest text-slate-500 hover:text-white hover:bg-slate-800 transition-all">Abort</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        function openBountyModal() { document.getElementById('bountyModal').classList.remove('hidden'); }
        function closeBountyModal() { document.getElementById('bountyModal').classList.add('hidden'); }

        async function submitBounty() {
            const btn = event.target;
            const title = document.getElementById('bTitle').value.trim();
            const desc = document.getElementById('bDesc').value.trim();
            
            if(!title || !desc) {
                // Shake animation for error (optional) or just alert
                alert("CRITICAL: DATA_MISSING");
                return;
            }

            const originalText = btn.innerHTML;
            btn.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> TRANSMITTING...`;
            btn.disabled = true;
            lucide.createIcons();

            try {
                const res = await fetch('api/post_bounty.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `title=${encodeURIComponent(title)}&desc=${encodeURIComponent(desc)}`
                });
                
                const data = await res.json();
                if(data.status === 'success') {
                    location.reload();
                } else {
                    alert("UPLINK_FAILURE: " + data.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (err) {
                console.error("NETWORK_VOID:", err);
                alert("OFFLINE: SERVER_NOT_RESPONDING");
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        async function fulfillBounty(id) {
            if(!confirm("CONFIRM_INTELLIGENCE_FULFILLMENT?")) return;

            try {
                const res = await fetch('api/fulfill_bounty.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `bounty_id=${id}`
                });
                const data = await res.json();
                
                if(data.status === 'success') {
                    // GSAP pulse effect before reload
                    gsap.to(`#bounty-${id}`, { opacity: 0.5, scale: 0.95, duration: 0.3, onComplete: () => location.reload() });
                }
            } catch (err) {
                console.error("FULFILLMENT_UPLINK_ERROR");
            }
        }

        async function loadBounties() {
            try {
                const res = await fetch('api/get_bounties.php');
                const html = await res.text();
                document.getElementById('bounty_container').innerHTML = html;
                lucide.createIcons();
            } catch(e) {
                console.error("SYNC_ERROR");
            }
        }
        
        loadBounties();
    </script>
</body>
</html>
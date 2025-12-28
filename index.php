<?php
include 'includes/connection.php';

// Bypass landing page if session exists
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aetheris Core | High-Grade Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Space+Grotesk:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #3b82f6; --bg: #020617; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: #f8fafc; overflow-x: hidden; }
        
        .cyber-grid { 
            background-image: linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .glass-vault { 
            background: rgba(15, 23, 42, 0.6); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255,255,255,0.05); 
        }

        .text-glow { text-shadow: 0 0 30px rgba(59, 130, 246, 0.4); }
        .architect-title { font-family: 'Space Grotesk', sans-serif; }

        @keyframes pulse-dot { 0% { opacity: 0.2; } 50% { opacity: 1; } 100% { opacity: 0.2; } }
        .ping { animation: pulse-dot 2s infinite; }
    </style>
</head>
<body class="cyber-grid min-h-screen">

    <nav class="fixed w-full z-50 px-10 py-6 flex justify-between items-center bg-slate-950/50 backdrop-blur-xl border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-6 bg-blue-600"></div>
            <h1 class="text-2xl font-black architect-title tracking-tighter uppercase italic">Aetheris<span class="text-blue-500">Core</span></h1>
        </div>
        <div class="flex items-center gap-10">
            <a href="login.php" class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 hover:text-white transition-all">Portal_Access</a>
            <a href="login.php" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-xl shadow-blue-900/40">
                Initialize_Link
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 pt-48">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div>
                <div class="inline-flex items-center gap-3 px-4 py-2 mb-8 rounded-full border border-blue-500/20 bg-blue-500/5 text-blue-500 text-[9px] font-black tracking-[0.4em] uppercase">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full ping"></span>
                    Network_Node: RJIT_STABLE
                </div>
                <h1 class="text-6xl md:text-8xl font-black architect-title tracking-tighter leading-[0.9] mb-8">
                    Sovereign <br>
                    <span class="text-blue-500 text-glow">Intelligence.</span>
                </h1>
                <p class="max-w-lg text-slate-500 text-lg leading-relaxed mb-12">
                    The Architect's proprietary environment for high-fidelity academic reconnaissance. 
                    Built for the 2022-26 elite.
                </p>
                <div class="flex gap-6">
                    <a href="login.php" class="px-10 py-5 bg-white text-black rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                        Establish_Connection
                    </a>
                </div>
            </div>

            <div class="glass-vault p-1 rounded-[3rem] border-white/10 shadow-2xl">
                <div class="bg-black/40 rounded-[2.8rem] p-10 font-mono text-xs overflow-hidden">
                    <div class="flex gap-2 mb-8 opacity-40">
                        <div class="w-2 h-2 rounded-full bg-white"></div>
                        <div class="w-2 h-2 rounded-full bg-white"></div>
                        <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <div class="space-y-3 text-blue-400/80">
                        <p>> Identifying Subject...</p>
                        <p class="text-white">> [GATE_OPEN] shiro_onigami authenticated.</p>
                        <p>> Fetching Apex_Solution_Matrix...</p>
                        <p>> 42_Files_Synced_to_Local_Vault.</p>
                        <p class="text-emerald-500 font-bold">> READY: Command Center Operational.</p>
                        <div class="w-2 h-4 bg-blue-600 animate-pulse mt-4"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-40 mb-32">
            <div class="glass-vault p-10 rounded-[2.5rem] border-white/5 group hover:border-blue-500/30 transition-all">
                <i data-lucide="layers" class="text-blue-500 w-8 h-8 mb-6"></i>
                <h3 class="text-lg font-bold uppercase tracking-tight mb-2">Apex Matrix</h3>
                <p class="text-slate-500 text-sm">Direct GitHub recon streams for verified solutions.</p>
            </div>
            <div class="glass-vault p-10 rounded-[2.5rem] border-white/5 group hover:border-blue-500/30 transition-all">
                <i data-lucide="shield-check" class="text-blue-500 w-8 h-8 mb-6"></i>
                <h3 class="text-lg font-bold uppercase tracking-tight mb-2">The Vault</h3>
                <p class="text-slate-500 text-sm">Encrypted indexing for certifications and professional data.</p>
            </div>
            <div class="glass-vault p-10 rounded-[2.5rem] border-white/5 group hover:border-blue-500/30 transition-all">
                <i data-lucide="network" class="text-blue-500 w-8 h-8 mb-6"></i>
                <h3 class="text-lg font-bold uppercase tracking-tight mb-2">Global Nodes</h3>
                <p class="text-slate-500 text-sm">P2P audio channels and encrypted intelligence broadcasts.</p>
            </div>
        </div>
    </main>

    <footer class="py-20 border-t border-white/5 text-center">
        <p class="text-[10px] font-black text-slate-700 uppercase tracking-[1em]">Aetheris_Core // Architect_Shiro_Onigami</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
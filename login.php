<?php
// login.php - The Secure Gateway to Aetheris Core
include 'includes/connection.php';

// Bypass if already authenticated
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
    <title>Gateway_Entry | Aetheris Core</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Space+Grotesk:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #3b82f6; --bg: #020617; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: #f8fafc; overflow: hidden; }
        .cyber-grid { 
            background-image: linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        .glass-vault { 
            background: rgba(15, 23, 42, 0.8); 
            backdrop-filter: blur(24px); 
            border: 1px solid rgba(255,255,255,0.05); 
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
        }
        .input-glow:focus { 
            border-color: var(--accent); 
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.1); 
        }
        .architect-title { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 cyber-grid">

    <div class="absolute top-1/4 -left-20 w-80 h-80 bg-blue-600/10 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-purple-600/10 blur-[120px] rounded-full"></div>

    <div class="glass-vault w-full max-w-md p-10 rounded-[2.5rem] relative z-10 border-t border-white/10">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-blue-600/10 border border-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                <i data-lucide="shield-chevron" class="text-blue-500 w-8 h-8"></i>
            </div>
            <h1 class="text-4xl font-black architect-title tracking-tighter uppercase italic">Aetheris<span class="text-blue-500">Core</span></h1>
            <p class="text-[10px] text-slate-500 font-mono mt-3 tracking-[0.4em] uppercase">Security_Protocol_v2.0</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Identity_Endpoint</label>
                <div class="relative">
                    <input type="email" id="email" required 
                           class="w-full p-4 pl-12 rounded-2xl bg-black/40 border border-white/5 input-glow outline-none transition-all text-sm font-medium"
                           placeholder="0902cs23xxxx@rjit.ac.in">
                    <i data-lucide="at-sign" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 w-4 h-4"></i>
                </div>
            </div>
            
            <button type="submit" id="submitBtn"
                    class="w-full py-5 bg-blue-600 hover:bg-blue-500 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-blue-900/40 flex items-center justify-center gap-3 group">
                Establish_Connection
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </button>
        </button>

        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-white/5"></span></div>
            <div class="relative flex justify-center text-[10px] uppercase font-black tracking-widest">
                <span class="bg-[#0b1120] px-4 text-slate-600">OR_USE_EXT_IDENTITY</span>
            </div>
        </div>

        <button type="button" onclick="initiateGoogleLogin()" 
                class="w-full py-4 bg-white/5 border border-white/10 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-white/10 transition-all flex items-center justify-center gap-3">
            <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-4 h-4">
            Sign_In_With_Google
        </button>

        <div class="mt-10 pt-6 border-t border-white/5 flex flex-col items-center gap-4">
            <div class="flex gap-6">
                <i data-lucide="fingerprint" class="text-slate-700 w-4 h-4"></i>
                <i data-lucide="cpu" class="text-slate-700 w-4 h-4"></i>
                <i data-lucide="network" class="text-slate-700 w-4 h-4"></i>
            </div>
            <p class="text-[9px] text-slate-600 font-mono text-center leading-loose">
                RESTRICTED ACCESS: RJIT BATCH 22-26<br>
                SUBJECTS UNDER MONITORING: ACTIVE
            </p>
        </div>
    </div>

    <?php if(isset($_GET['action']) && $_GET['action'] === 'logout'): ?>
    <script>
        Swal.fire({
            title: 'TERMINATING_SESSION',
            text: 'Wiping local cache and establishing logout protocol...',
            icon: 'info',
            background: '#020617',
            color: '#fff',
            timer: 2000,
            showConfirmButton: false,
            willClose: () => {
                window.location.href = 'api/logout_handler.php';
            }
        });
    </script>
    <?php endif; ?>

    <script src="js/auth.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

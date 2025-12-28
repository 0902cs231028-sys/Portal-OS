<?php
include 'includes/connection.php';
// Hard security gate
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
$me_res = mysqli_query($conn, "SELECT full_name, profile_pic, roll_no, role FROM students WHERE student_id = '$uid'");
$me = mysqli_fetch_assoc($me_res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Uplink | PortalOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background: #020617; color: #f8fafc; font-family: 'Inter', sans-serif; overflow: hidden; }
        .glass-header { background: rgba(2, 6, 23, 0.8); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        
        /* The Cyber Grid Background */
        .cyber-grid { 
            background-image: linear-gradient(rgba(59, 130, 246, 0.02) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(59, 130, 246, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="flex flex-col h-screen cyber-grid relative">

    <header class="glass-header px-6 py-4 flex justify-between items-center z-20 shrink-0 h-20">
        <div class="flex items-center gap-6">
            <a href="dashboard.php" class="p-3 bg-white/5 border border-white/5 rounded-2xl hover:bg-blue-600/20 hover:text-blue-500 transition-all text-slate-400 group">
                <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic text-white">Global<span class="text-blue-500">_Uplink</span></h1>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                    <p class="text-[9px] text-slate-500 font-mono tracking-[0.3em] uppercase">Secure_Channel_Active</p>
                </div>
            </div>
        </div>
        
        <div class="hidden md:flex items-center gap-4 bg-white/5 pr-2 pl-4 py-2 rounded-full border border-white/5">
            <div class="text-right">
                <p class="text-[10px] text-white font-bold uppercase tracking-widest"><?php echo $me['full_name']; ?></p>
            </div>
            <img src="<?php echo $me['profile_pic'] ?: 'assets/profile.png'; ?>" class="w-8 h-8 rounded-full object-cover border border-white/10">
        </div>
    </header>

    <main id="chatBox" class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar scroll-smooth pb-32">
        <div class="flex flex-col items-center justify-center h-full opacity-50">
            <i data-lucide="loader-2" class="w-8 h-8 animate-spin mb-4 text-blue-500"></i>
            <p class="text-[10px] mono uppercase tracking-widest">Establishing_Uplink...</p>
        </div>
    </main>

    <footer class="absolute bottom-0 w-full p-6 bg-gradient-to-t from-[#020617] via-[#020617]/95 to-transparent z-30">
        <form id="chatForm" class="max-w-4xl mx-auto relative flex gap-4 items-end">
            <div class="flex-1 relative group">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-purple-600 rounded-[2rem] opacity-20 group-focus-within:opacity-50 transition duration-500 blur"></div>
                <input type="text" id="msgInput" autocomplete="off"
                       placeholder="Broadcast transmission..." 
                       class="relative w-full bg-[#0f172a] border border-white/10 p-5 pl-6 rounded-[1.8rem] outline-none text-sm font-medium text-white placeholder:text-slate-600 shadow-2xl focus:bg-slate-900 transition-all">
            </div>
            <button type="submit" class="h-[60px] w-[60px] bg-blue-600 hover:bg-blue-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-blue-900/40 hover:scale-105 transition-all">
                <i data-lucide="send" class="w-5 h-5 ml-1"></i>
            </button>
        </form>
    </footer>

    <script src="js/message.js"></script> 
    <script>lucide.createIcons();</script>
</body>
</html>
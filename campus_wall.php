<?php
include 'includes/connection.php';
// Gatekeeper: Ensure only authenticated nodes access the feed
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
// Fetch current user details for the header
$me_query = mysqli_query($conn, "SELECT profile_pic FROM students WHERE student_id = '$uid'");
$me_data = mysqli_fetch_assoc($me_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network_Feed | Aetheris Core</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        body { background: #020617; color: #f8fafc; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .cyber-grid { 
            background-image: linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .post-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .post-card:hover { border-color: rgba(59, 130, 246, 0.5); transform: translateY(-5px); box-shadow: 0 20px 40px -20px rgba(59, 130, 246, 0.3); }
        .masonry { column-count: 1; column-gap: 1.5rem; }
        @media (min-width: 768px) { .masonry { column-count: 2; } }
        @media (min-width: 1280px) { .masonry { column-count: 3; } }
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.2); border-radius: 10px; }
    </style>
</head>
<body class="cyber-grid min-h-screen">

    <nav class="sticky top-0 z-50 bg-[#020617]/80 backdrop-blur-xl border-b border-white/5 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-3 bg-white/5 rounded-2xl hover:bg-blue-600/10 hover:text-blue-500 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-black italic tracking-tighter">NETWORK<span class="text-blue-500">_FEED</span></h1>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="fetchFeed()" class="p-3 bg-white/5 rounded-2xl hover:bg-white/10 text-slate-400 transition-all">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </button>
                <div class="h-10 w-[1px] bg-white/10 mx-2"></div>
                <img src="<?php echo $me_data['profile_pic']; ?>" class="w-10 h-10 rounded-xl border border-blue-500/30 object-cover" onerror="this.src='assets/profile.png'">
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-10">
        <div class="mb-12 glass-card p-8 rounded-[2.5rem] border border-white/5 bg-white/5">
            <div class="flex gap-6 items-start">
                <img src="<?php echo $me_data['profile_pic']; ?>" class="w-14 h-14 rounded-2xl object-cover border-2 border-blue-500/20" onerror="this.src='assets/profile.png'">
                <div class="flex-1">
                    <textarea id="broadcast_content" placeholder="Initiate global transmission..." 
                              class="w-full bg-transparent border-none outline-none text-xl font-light text-slate-300 resize-none h-20 placeholder:opacity-30" maxlength="500"></textarea>
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-white/5">
                        <div class="flex gap-4">
                            <button class="text-slate-500 hover:text-blue-500 transition-all"><i data-lucide="image" class="w-5 h-5"></i></button>
                            <button class="text-slate-500 hover:text-blue-500 transition-all"><i data-lucide="hash" class="w-5 h-5"></i></button>
                        </div>
                        <button onclick="submitBroadcast()" class="px-8 py-3 bg-blue-600 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-blue-500 shadow-lg shadow-blue-900/40 transition-all">
                            Broadcast_Signal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="masonry" id="wall_feed">
            </div>
    </main>

    <script>
        lucide.createIcons();

        async function fetchFeed() {
            const feed = document.getElementById('wall_feed');
            // GSAP exit animation
            gsap.to(feed.children, { opacity: 0, y: 20, stagger: 0.05, duration: 0.3 });
            
            const response = await fetch('api/get_posts.php');
            const html = await response.text();
            
            setTimeout(() => {
                feed.innerHTML = html;
                lucide.createIcons();
                // GSAP enter animation
                gsap.fromTo(".masonry-item", 
                    { opacity: 0, y: 30, scale: 0.9 }, 
                    { opacity: 1, y: 0, scale: 1, stagger: 0.1, duration: 0.6, ease: "power4.out" }
                );
            }, 300);
        }

        async function submitBroadcast() {
            const content = document.getElementById('broadcast_content');
            if (!content.value.trim()) return;

            const formData = new FormData();
            formData.append('content', content.value);

            const res = await fetch('api/create_post.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.status === 'success') {
                content.value = '';
                fetchFeed();
            }
        }

        fetchFeed();
        // Live stream sync
        setInterval(fetchFeed, 60000);
    </script>
</body>
</html>

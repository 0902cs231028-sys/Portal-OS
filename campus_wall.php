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

        .comments-section { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; }
        .comments-section.open { max-height: 600px; transition: max-height 0.6s ease-in; }
    </style>
</head>
<body class="cyber-grid min-h-screen">

    <nav class="sticky top-0 z-50 bg-[#020617]/80 backdrop-blur-xl border-b border-white/5 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-3 bg-white/5 rounded-2xl hover:bg-blue-600/10 hover:text-blue-500 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-black italic tracking-tighter">CAMPUS<span class="text-blue-500">_WALL</span></h1>
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
                        </div>
                        <button onclick="submitBroadcast()" class="px-8 py-3 bg-blue-600 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-blue-500 shadow-lg shadow-blue-900/40 transition-all">
                            Broadcast_Signal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="masonry" id="wall_feed"></div>
    </main>

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
            <div id="dmFeed" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar"></div>
            <div class="p-6 border-t border-white/5">
                <form id="dmForm" class="relative">
                    <input type="hidden" id="dmTargetId">
                    <input type="text" id="dmInput" autocomplete="off" placeholder="Enter encrypted transmission..." class="w-full bg-black/40 border border-white/10 p-5 pr-16 rounded-[2rem] outline-none focus:border-blue-600 transition-all text-sm font-medium">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-500 transition-all shadow-lg shadow-blue-900/40"><i data-lucide="send" class="w-5 h-5"></i></button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/dm_system.js"></script>
    <script>
        lucide.createIcons();
/**
 * ARCHITECT TERMINATION PROTOCOL
 * Permanent removal of a post from the global feed.
 */
async function deletePost(postId) {
    // 1. Confirmation Gate
    if (!confirm("TERMINATE BROADCAST? This action is permanent.")) return;

    try {
        const formData = new FormData();
        formData.append('post_id', postId);

        // 2. Execute Uplink to Termination Protocol
        const response = await fetch('api/delete_post.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        // 3. UI Synchronization
        if (result.status === 'success') {
            // Remove the element immediately for zero-latency feel
            const postElement = document.getElementById(`post-${postId}`);
            if (postElement) {
                postElement.style.opacity = '0';
                postElement.style.transform = 'scale(0.9)';
                setTimeout(() => fetchFeed(), 300); // Re-run masonry layout
            }
        } else {
            alert("TERMINATION FAILED: " + result.message);
        }
    } catch (err) {
        console.error("UPLINK_FAILURE:", err);
    }
}
        // 1. Core Feed Loader
        async function fetchFeed() {
            const feed = document.getElementById('wall_feed');
            try {
                const response = await fetch('api/get_posts.php');
                const html = await response.text();
                feed.innerHTML = html;
                lucide.createIcons();
                
                gsap.fromTo(".masonry-item", 
                    { opacity: 0, y: 30 }, 
                    { opacity: 1, y: 0, stagger: 0.1, duration: 0.5, ease: "power2.out" }
                );
            } catch (err) { console.error("Feed Error:", err); }
        }

        // 2. Broadcast Submission
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

        // 3. INTERACTION ENGINE (Likes & Comments)
        async function handleInteraction(postId, action) {
            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('action', action);
            
            if (action === 'comment') {
                const input = document.getElementById(`input-${postId}`);
                if (!input) return;
                const text = input.value.trim();
                if (!text) return;
                formData.append('comment_text', text);
            }

            try {
                const res = await fetch('api/interact_post.php', { method: 'POST', body: formData });
                const data = await res.json();

                if (data.status === 'success') {
                    fetchFeed(); // Sync the UI
                }
            } catch (err) { console.error("Interaction Failure:", err); }
        }

        // 4. UI: Toggle Comments
        function toggleComments(postId) {
            const section = document.getElementById(`comments-${postId}`);
            const chevron = document.getElementById(`chevron-${postId}`);
            
            if (section.classList.contains('open')) {
                section.classList.remove('open');
                if(chevron) chevron.style.transform = "rotate(0deg)";
            } else {
                section.classList.add('open');
                if(chevron) chevron.style.transform = "rotate(180deg)";
            }
        }

        // Initial Boot
        fetchFeed();
        setInterval(fetchFeed, 60000); 
    </script>
</body>
</html>

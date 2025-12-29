<?php
include 'includes/connection.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
$me_query = mysqli_query($conn, "SELECT profile_pic FROM students WHERE student_id = '$uid'");
$me_data = mysqli_fetch_assoc($me_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bounty_Hub | Aetheris Core</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #020617; color: #f8fafc; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .cyber-grid { 
            background-image: linear-gradient(rgba(245, 158, 11, 0.03) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(245, 158, 11, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .bounty-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .bounty-card:hover { border-color: rgba(245, 158, 11, 0.5); transform: translateY(-5px); box-shadow: 0 20px 40px -20px rgba(245, 158, 11, 0.3); }
        .masonry { column-count: 1; column-gap: 1.5rem; }
        @media (min-width: 768px) { .masonry { column-count: 2; } }
        @media (min-width: 1280px) { .masonry { column-count: 3; } }
        .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; }
    </style>
</head>
<body class="cyber-grid min-h-screen">
    <nav class="sticky top-0 z-50 bg-[#020617]/80 backdrop-blur-xl border-b border-white/5 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="p-3 bg-white/5 rounded-2xl hover:bg-amber-600/10 hover:text-amber-500 transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-2xl font-black italic tracking-tighter uppercase">Bounty<span class="text-amber-500">_Hub</span></h1>
            </div>
            <button onclick="fetchFeed()" class="p-3 bg-white/5 rounded-2xl hover:bg-white/10 text-slate-400 transition-all">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </button>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-10">
        <div class="mb-12 glass-card p-8 rounded-[2.5rem] border border-white/5 bg-white/5">
            <div class="flex gap-6 items-start">
                <img src="<?php echo $me_data['profile_pic']; ?>" class="w-14 h-14 rounded-2xl object-cover border-2 border-amber-500/20" onerror="this.src='assets/profile.png'">
                <div class="flex-1">
                    <input type="text" id="bounty_title" placeholder="Contract_Objective" class="w-full bg-transparent border-none outline-none text-lg font-black text-white placeholder:opacity-30 mb-2 uppercase italic">
                    <textarea id="bounty_desc" placeholder="Describe resource..." class="w-full bg-transparent border-none outline-none text-sm font-medium text-slate-300 resize-none h-20 placeholder:opacity-30" maxlength="500"></textarea>
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-white/5">
                        <button onclick="submitBounty()" class="px-8 py-3 bg-amber-600 rounded-xl font-black text-[10px] tracking-widest uppercase hover:bg-amber-500 transition-all">
                            Initialize_Contract
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="masonry" id="wall_feed"></div>
    </main>

    <script src="js/dm_system.js"></script>
    <script>
        lucide.createIcons();

        async function fetchFeed() {
            const feed = document.getElementById('wall_feed');
            try {
                const response = await fetch('api/get_bounties.php');
                const html = await response.text();
                feed.innerHTML = html;
                lucide.createIcons();
                if (document.querySelectorAll('.bounty-card').length > 0) {
                    gsap.fromTo(".bounty-card", { opacity: 0, y: 30 }, { opacity: 1, y: 0, stagger: 0.1, duration: 0.5 });
                }
            } catch (err) { console.error("BOUNTY_LOAD_ERROR"); }
        }

        async function submitBounty() {
            const title = document.getElementById('bounty_title').value;
            const desc = document.getElementById('bounty_desc').value;
            if (!title.trim() || !desc.trim()) return;

            const formData = new FormData();
            formData.append('title', title);
            formData.append('desc', desc);

            try {
                const res = await fetch('api/post_bounty.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'success') {
                    document.getElementById('bounty_title').value = '';
                    document.getElementById('bounty_desc').value = '';
                    Swal.fire({ icon: 'success', title: 'Contract Initialized', background: '#020617', color: '#fff' });
                    fetchFeed(); 
                }
            } catch (err) { console.error("SUBMIT_FAIL"); }
        }

        // --- THE FULL FUNCTIONS PLACED CORRECTLY ---
        async function approveBounty(bountyId) {
            const formData = new FormData();
            formData.append('bounty_id', bountyId);
            try {
                const res = await fetch('api/approve_bounty.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Contract Authorized', background: '#020617', color: '#fff' });
                    fetchFeed();
                }
            } catch (err) { console.error("APPROVE_FAIL"); }
        }
async function deleteBounty(bountyId) {
    const { isConfirmed } = await Swal.fire({
        title: 'PURGE CONTRACT?',
        text: "This bounty will be permanently erased from the matrix.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#1e293b',
        confirmButtonText: 'DELETE',
        background: '#020617',
        color: '#fff'
    });

    if (isConfirmed) {
        const formData = new FormData();
        formData.append('bounty_id', bountyId);
        try {
            const res = await fetch('api/delete_bounty.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Data Purged', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#020617', color: '#fff' });
                fetchFeed();
            }
        } catch (err) { console.error("DELETE_FAIL"); }
    }
}

async function fulfillBounty(bountyId) {
    const formData = new FormData();
    formData.append('bounty_id', bountyId);
    try {
        const res = await fetch('api/fulfill_bounty.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Contract Resolved', background: '#020617', color: '#fff' });
            fetchFeed();
        }
    } catch (err) { console.error("RESOLVE_FAIL"); }
}
        
        async function fulfillBounty(bountyId) {
            const formData = new FormData();
            formData.append('bounty_id', bountyId);
            try {
                const res = await fetch('api/fulfill_bounty.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Contract Resolved', background: '#020617', color: '#fff' });
                    fetchFeed();
                }
            } catch (err) { console.error("RESOLVE_FAIL"); }
        }

        fetchFeed();
    </script>
</body>
</html>

<?php
include 'includes/connection.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '$uid'");
$user = mysqli_fetch_assoc($user_query);

$res_query = mysqli_query($conn, "SELECT * FROM student_resume_data WHERE student_id = '$uid'");
$resume = mysqli_fetch_assoc($res_query);

$p_pic = (!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png') ? $user['profile_pic'] : 'assets/profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Identity | PortalOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background: #020617; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(30, 41, 59, 0.3); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); }
        .input-supreme { background: rgba(0,0,0,0.3) !important; border: 1px solid rgba(255,255,255,0.1) !important; color: white !important; }
        .input-supreme:focus { border-color: #3b82f6 !important; box-shadow: 0 0 20px rgba(59, 130, 246, 0.1); }
    </style>
</head>
<body class="p-4 md:p-10">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-5xl font-black tracking-tighter uppercase italic text-white">Identity<span class="text-blue-500">_Core</span></h1>
                <p class="text-slate-500 font-mono text-xs mt-2 tracking-[0.3em]">PROFILING_SYSTEM_v4.0</p>
            </div>
            <a href="dashboard.php" class="px-8 py-4 bg-slate-900 border border-white/5 rounded-2xl hover:bg-slate-800 transition-all flex items-center gap-3 font-bold group">
                <i data-lucide="arrow-left" class="group-hover:-translate-x-1 transition-transform"></i> DASHBOARD
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <div class="lg:col-span-4 space-y-6">
                <div class="glass-card p-10 rounded-[3rem] text-center sticky top-10 border-blue-500/20">
                    <div class="relative w-44 h-44 mx-auto mb-8 group">
                        <img id="profile_display" src="<?php echo $p_pic; ?>" class="rounded-3xl w-full h-full object-cover border-2 border-blue-500/30 shadow-2xl shadow-blue-500/10">
                        <label for="pic_upload" class="absolute -bottom-2 -right-2 bg-blue-600 w-12 h-12 rounded-2xl flex items-center justify-center cursor-pointer hover:scale-110 transition-all shadow-xl">
                            <i data-lucide="camera" class="w-5 h-5"></i>
                            <input type="file" id="pic_upload" class="hidden" accept="image/*" onchange="compressAndPreview(this)">
                        </label>
                    </div>
                    
                    <h2 class="text-2xl font-black uppercase"><?php echo $user['full_name']; ?></h2>
                    <p class="text-blue-500 font-mono text-[10px] tracking-[0.4em] mt-2 uppercase">
                        <?php echo $user['branch']; ?> UNIT // BATCH 20<?php echo $user['batch_year']; ?>
                    </p>

                    <div class="mt-10 space-y-4">
                        <button onclick="saveProfile()" class="w-full py-5 bg-blue-600 hover:bg-blue-500 rounded-2xl font-black transition-all shadow-xl shadow-blue-900/40 flex items-center justify-center gap-3">
                            <i data-lucide="save"></i> SYNC_CHANGES
                        </button>
                    </div>
                </div>

                <div class="glass-card p-8 rounded-[2.5rem] border-purple-500/20 bg-purple-500/5">
                    <h3 class="text-sm font-black uppercase tracking-widest text-purple-400 mb-4 flex items-center gap-2">
                        <i data-lucide="radio" class="w-4 h-4"></i> Global Broadcast
                    </h3>
                    <textarea id="broadcastContent" class="w-full p-4 rounded-xl input-supreme text-xs h-24 mb-4 outline-none" placeholder="What's happening on campus?"></textarea>
                    <button onclick="submitBroadcast()" class="w-full py-3 bg-purple-600 rounded-xl font-bold text-xs">TRANSMIT_TO_WALL</button>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-8">
                
                <form id="supremeProfileForm" class="space-y-6">
                    <input type="hidden" name="profile_img_base64" id="profile_img_base64">
                    
                    <div class="glass-card p-10 rounded-[3rem]">
                        <div class="flex items-center gap-4 mb-8">
                            <i data-lucide="user-round" class="text-blue-500"></i>
                            <h3 class="text-xl font-black uppercase italic">Executive_Summary</h3>
                        </div>
                        <textarea name="summary" id="live_summary" rows="4" class="w-full p-6 rounded-[2rem] input-supreme outline-none font-medium" 
                        onkeyup="updatePreview()" placeholder="Strategic focus..."><?php echo $resume['professional_summary'] ?? ''; ?></textarea>
                    </div>

                    <div class="glass-card p-10 rounded-[3rem]">
                        <div class="flex items-center gap-4 mb-8 text-emerald-500">
                            <i data-lucide="code-2"></i>
                            <h3 class="text-xl font-black uppercase italic text-white">Stack_Registry</h3>
                        </div>
                        <input type="text" name="skills" id="live_skills" class="w-full p-6 rounded-[2rem] input-supreme outline-none font-bold tracking-tight" 
                        onkeyup="updatePreview()" placeholder="PHP, MySQL, WebRTC..." value="<?php echo $resume['skills_json'] ?? ''; ?>">
                    </div>
                </form>

                <div class="glass-card p-12 rounded-[3.5rem] shadow-2xl bg-white/[0.01] border-white/5 mt-10 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-8">
                        <i data-lucide="layers" class="text-white/5 w-32 h-32"></i>
                    </div>

                    <div class="flex justify-between items-start border-b border-white/10 pb-10 mb-10">
                        <div>
                            <h2 class="text-4xl font-black text-white tracking-tighter"><?php echo $user['full_name']; ?></h2>
                            <p class="text-blue-500 font-mono text-sm tracking-[0.3em] mt-2 uppercase">
                                <?php echo $user['branch']; ?> // Class of 20<?php echo $user['batch_year']; ?>
                            </p>
                        </div>
                        <a href="generate_resume.php" target="_blank" class="px-8 py-5 bg-white text-black rounded-3xl font-black hover:scale-105 transition-all flex items-center gap-3 shadow-2xl">
                            <i data-lucide="file-down"></i> EXPORT_PDF
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
                        <div class="md:col-span-8">
                            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] mb-6">Professional_Profile</h4>
                            <p id="preview_summary" class="text-lg leading-relaxed text-slate-300 font-light italic">
                                <?php echo $resume['professional_summary'] ?? 'Update your summary to see the live rendering...'; ?>
                            </p>
                        </div>
                        <div class="md:col-span-4">
                            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] mb-6">Expertise_Grid</h4>
                            <div id="preview_skills" class="flex flex-wrap gap-2">
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /** * AETHERIS CORE - Identity Synchronization Engine
 */

// 1. High-Performance Image Compression & Preview
window.compressAndPreview = (input) => {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => {
        const img = new Image();
        img.src = e.target.result;
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 400; // Optimal for Profile Identity Nodes
            const scaleSize = MAX_WIDTH / img.width;
            canvas.width = MAX_WIDTH;
            canvas.height = img.height * scaleSize;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            // Export as high-quality JPEG Base64
            const compressedBase64 = canvas.toDataURL('image/jpeg', 0.8);
            
            // Sync with UI and Hidden Input
            document.getElementById('profile_display').src = compressedBase64;
            document.getElementById('profile_img_base64').value = compressedBase64;
        };
    };
};

// 2. Multiprocessor Sync to Database
window.saveProfile = async () => {
    const btn = document.querySelector('button[onclick="saveProfile()"]');
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = `<i data-lucide="loader-2" class="animate-spin w-5 h-5"></i> SYNCING_MATRIX...`;
    btn.disabled = true;

    // Build the payload exactly as update_profile.php expects
    const formData = new FormData();
    formData.append('summary', document.getElementById('live_summary').value);
    formData.append('skills', document.getElementById('live_skills').value);
    formData.append('profile_img_base64', document.getElementById('profile_img_base64').value);

    try {
        const response = await fetch('api/update_profile.php', {
            method: 'POST',
            body: formData // Standard POST transmission
        });

        const data = await response.json();

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'IDENTITY_SYNCHRONIZED',
                text: 'Local data successfully committed to the database.',
                background: '#020617',
                color: '#fff',
                timer: 2000,
                showConfirmButton: false
            });
        }
    } catch (err) {
        console.error("SYNC_FAILURE:", err);
        Swal.fire({ icon: 'error', title: 'TRANSMISSION_ERROR', background: '#020617', color: '#ef4444' });
    } finally {
        btn.innerHTML = originalContent;
        btn.disabled = false;
        lucide.createIcons();
    }
};
        
        lucide.createIcons();

        function updatePreview() {
            const summary = document.getElementById('live_summary').value;
            const skills = document.getElementById('live_skills').value;
            
            document.getElementById('preview_summary').innerText = summary || 'Update your summary...';
            
            const skillsContainer = document.getElementById('preview_skills');
            skillsContainer.innerHTML = '';
            skills.split(',').forEach(skill => {
                if(skill.trim() !== "") {
                    const span = document.createElement('span');
                    span.className = "px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-xl text-[10px] text-blue-400 font-bold uppercase tracking-widest";
                    span.innerText = skill.trim();
                    skillsContainer.appendChild(span);
                }
            });
        }

        async function submitBroadcast() {
            const content = document.getElementById('broadcastContent').value;
            if(!content) return;

            const response = await fetch('api/submit_post.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `content=${encodeURIComponent(content)}`
            });
            const res = await response.json();
            if(res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'Broadcast Sent', text: 'Awaiting Architech Approval', background: '#1e293b', color: '#fff' });
                document.getElementById('broadcastContent').value = '';
            }
        }

        // Run preview on load
        window.onload = updatePreview;
    </script>
</body>
</html>

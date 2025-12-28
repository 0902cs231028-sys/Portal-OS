<?php
include 'includes/connection.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
// Fetch Vault items
$vault_query = mysqli_query($conn, "SELECT * FROM vault_items WHERE student_id = '$uid' ORDER BY vault_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Vault | PortalOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        body { background: #020617; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .cyber-grid { 
            background-image: linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px), 
                              linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .vault-card { 
            background: rgba(30, 41, 59, 0.3); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); transition: all 0.4s; 
        }
        .vault-card:hover { transform: translateY(-5px); border-color: #3b82f6; box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.3); }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
        
        #linkModal { transition: opacity 0.3s ease; }
    </style>
</head>
<body class="p-6 md:p-12 cyber-grid min-h-screen">

    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
            <div>
                <a href="dashboard.php" class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-500 transition-colors mb-4 font-mono text-xs uppercase tracking-widest">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i> Return_to_Terminal
                </a>
                <h1 class="text-6xl font-black tracking-tighter uppercase italic text-white">Digital <span class="text-blue-500">Vault</span></h1>
                <p class="text-slate-500 mt-3 font-mono text-[10px] tracking-[0.5em] uppercase">Encrypted_Storage // Node_ID: <?php echo $uid; ?></p>
            </div>
            
            <button onclick="openLinkModal()" class="px-8 py-5 bg-blue-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 transition-all shadow-2xl shadow-blue-900/40 flex items-center gap-3">
                <i data-lucide="upload-cloud" class="w-5 h-5"></i> 
                <span>Secure_Upload</span>
            </button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($item = mysqli_fetch_assoc($vault_query)): ?>
            <div class="vault-card p-8 rounded-[3rem] relative flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-500">
                            <i data-lucide="<?php echo ($item['doc_type'] == 'Image') ? 'image' : 'file-text'; ?>" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[9px] font-mono text-slate-600 uppercase tracking-widest">SECURE_ASSET</span>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-white mb-2 leading-tight truncate"><?php echo htmlspecialchars($item['doc_title']); ?></h3>
                    <p class="text-blue-500 text-[10px] font-mono uppercase tracking-[0.3em] italic"><?php echo htmlspecialchars($item['doc_type']); ?></p>
                </div>
                
                <div class="mt-12 flex gap-4">
                    <button onclick="openMatrix('<?php echo htmlspecialchars($item['google_drive_link']); ?>', '<?php echo addslashes($item['doc_title']); ?>')" 
                            class="flex-1 py-4 bg-slate-900 border border-white/5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-blue-500"></i> Decrypt_&_View
                    </button>
                    
                    <button onclick="deleteVaultItem(<?php echo $item['vault_id']; ?>)" class="w-14 h-14 flex items-center justify-center bg-red-600/5 text-red-500/40 border border-red-500/10 rounded-2xl hover:bg-red-600 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="linkModal" class="hidden fixed inset-0 z-[300] bg-slate-950/95 backdrop-blur-2xl flex items-center justify-center p-6">
        <div class="glass-card p-12 rounded-[4rem] max-w-xl w-full border border-blue-500/20 shadow-2xl">
            <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter mb-8">Smart_Uplink</h2>
            
            <div class="space-y-6">
                <div class="relative group">
                    <input type="file" id="vFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="smartDetect(this)">
                    <div class="w-full p-6 rounded-2xl bg-blue-600/10 border border-blue-500/30 flex items-center justify-between group-hover:bg-blue-600/20 transition-all">
                        <span id="fileNameDisplay" class="text-sm font-bold text-blue-400">1. Click to Select File...</span>
                        <i data-lucide="upload" class="w-5 h-5 text-blue-500"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 ml-2">Document Title</label>
                    <input type="text" id="vTitle" placeholder="Waiting for file..." class="w-full p-5 rounded-2xl bg-black/40 border border-white/10 outline-none focus:border-blue-600 transition-all text-sm font-medium text-white">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 ml-2">Category Classification</label>
                    <select id="vType" class="w-full p-5 rounded-2xl bg-black/40 border border-white/10 outline-none focus:border-blue-600 transition-all text-sm font-medium text-slate-400">
                        <option value="Certification">Certification</option>
                        <option value="Internship">Internship Letter</option>
                        <option value="Academic">Academic Record</option>
                        <option value="Project">Project Documentation</option>
                        <option value="Image">Identity Image</option>
                    </select>
                </div>

                <div class="flex gap-4 pt-6">
                    <button onclick="submitVaultItem()" class="flex-1 py-5 bg-blue-600 rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-blue-500 transition-all">Start_Encryption</button>
                    <button onclick="closeLinkModal()" class="px-10 py-5 bg-slate-800 rounded-3xl font-black text-xs uppercase tracking-widest text-slate-500 hover:text-white">Abort</button>
                </div>
            </div>
        </div>
    </div>

    <div id="matrixViewer" class="hidden fixed inset-0 z-[400] bg-slate-950/98 backdrop-blur-3xl flex flex-col">
        <div class="h-24 border-b border-white/5 flex items-center justify-between px-10">
            <div class="flex items-center gap-4">
                <i data-lucide="lock" class="text-emerald-500 w-5 h-5"></i>
                <div>
                    <h3 id="matrixFileName" class="font-black text-sm uppercase tracking-widest text-white">Secure_View</h3>
                    <p class="text-[9px] text-emerald-500 font-mono tracking-widest uppercase">Decryption_Successful</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <a id="matrixDownload" href="#" target="_blank" class="px-6 py-3 bg-blue-600/10 text-blue-500 border border-blue-500/20 rounded-xl font-black text-[10px] hover:bg-blue-600/20 transition-all flex items-center gap-2">
                    <i data-lucide="download" class="w-3 h-3"></i> EXTRACT
                </a>

                <button onclick="closeMatrix()" class="px-8 py-3 bg-red-600/10 text-red-500 border border-red-500/20 rounded-xl font-black text-[10px] hover:bg-red-600/20 transition-all">
                    CLOSE_SESSION
                </button>
            </div>
        </div>
        <iframe id="matrixFrame" class="w-full h-full border-none bg-black/20" src=""></iframe>
    </div>

    <script>
        lucide.createIcons();
        function openLinkModal() { document.getElementById('linkModal').classList.remove('hidden'); }
        function closeLinkModal() { document.getElementById('linkModal').classList.add('hidden'); }
        
        // --- FIXED MATRIX ENGINE (Internalized to ensure parameters match) ---
        const MatrixEngine = {
            viewer: document.getElementById('matrixViewer'),
            frame: document.getElementById('matrixFrame'),
            title: document.getElementById('matrixFileName'),
            download: document.getElementById('matrixDownload'),

            open: function(filePath, fileName) {
                // HUD Setup
                this.title.innerText = `[RECON] DECRYPTING: ${fileName.toUpperCase()}...`;
                this.title.style.color = "#3b82f6"; 

                // FIX: Use 'path' to match view_vault.php's requirement
                const bridgeUrl = `api/view_vault.php?path=${encodeURIComponent(filePath)}`;
                
                // Set Download Link
                if(this.download) {
                    this.download.href = bridgeUrl; // No download=true needed, bridge sends headers
                }

                // Animation
                this.viewer.classList.remove('hidden');
                if(typeof gsap !== 'undefined') {
                    gsap.fromTo(this.viewer, 
                        { opacity: 0, scale: 1.1, backdropFilter: "blur(0px)" }, 
                        { duration: 0.5, opacity: 1, scale: 1, backdropFilter: "blur(20px)", ease: "expo.out" }
                    );
                }
                document.body.style.overflow = 'hidden';

                // Load Content
                this.frame.src = bridgeUrl;

                this.frame.onload = () => {
                    this.title.innerText = fileName.toUpperCase();
                    this.title.style.color = "#10b981"; 
                    this.title.classList.add('animate-pulse');
                };
            },

            close: function() {
                if(typeof gsap !== 'undefined') {
                    gsap.to(this.viewer, { 
                        duration: 0.4, opacity: 0, scale: 0.9, ease: "power4.in",
                        onComplete: () => {
                            this.viewer.classList.add('hidden');
                            this.frame.src = ""; 
                            document.body.style.overflow = 'auto';
                            this.title.classList.remove('animate-pulse');
                        }
                    });
                } else {
                    this.viewer.classList.add('hidden');
                    this.frame.src = "";
                    document.body.style.overflow = 'auto';
                }
            }
        };

        window.openMatrix = (url, name) => MatrixEngine.open(url, name);
        window.closeMatrix = () => MatrixEngine.close();

        // --- UPLOAD LOGIC ---
        function smartDetect(input) {
            if(input.files && input.files[0]) {
                const file = input.files[0];
                const name = file.name;
                const nameLower = name.toLowerCase();

                document.getElementById('fileNameDisplay').innerText = name;
                document.getElementById('fileNameDisplay').classList.remove('text-blue-400');
                document.getElementById('fileNameDisplay').classList.add('text-white');

                const cleanName = name.replace(/\.[^/.]+$/, "").replace(/[-_]/g, " ");
                const titleInput = document.getElementById('vTitle');
                if(!titleInput.value) { 
                    titleInput.value = cleanName;
                }

                const typeSelect = document.getElementById('vType');
                if (nameLower.match(/(cert|course|aws|udemy|completion|degree)/)) typeSelect.value = "Certification";
                else if (nameLower.match(/(intern|offer|letter|joining|job)/)) typeSelect.value = "Internship";
                else if (nameLower.match(/(mark|grade|result|card|sem|academic)/)) typeSelect.value = "Academic";
                else if (nameLower.match(/(project|report|doc|ppt)/)) typeSelect.value = "Project";
                else if (nameLower.match(/(jpg|png|jpeg|img|photo)/)) typeSelect.value = "Image";
                
                typeSelect.classList.add('text-blue-400');
                titleInput.classList.add('text-blue-400');
            }
        }
        
        async function submitVaultItem() {
            const title = document.getElementById('vTitle').value;
            const type = document.getElementById('vType').value;
            const fileInput = document.getElementById('vFile');

            if(!title || !fileInput.files[0]) { Swal.fire('Error', 'Missing Data', 'error'); return; }

            const formData = new FormData();
            formData.append('title', title);
            formData.append('type', type);
            formData.append('file', fileInput.files[0]);

            const btn = document.querySelector('button[onclick="submitVaultItem()"]');
            const originalText = btn.innerText;
            btn.innerText = "ENCRYPTING & UPLOADING..."; btn.disabled = true;

            try {
                const res = await fetch('api/save_vault.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Secured', text: 'Asset stored in private cloud.', timer: 1500, showConfirmButton: false, background: '#020617', color: '#fff' });
                    setTimeout(() => location.reload(), 1500);
                } else { throw new Error(data.message); }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Upload Failed', text: err.message, background: '#020617', color: '#ef4444' });
            } finally {
                btn.innerText = originalText; btn.disabled = false;
            }
        }

        async function deleteVaultItem(vid) {
            if((await Swal.fire({ title: 'Delete?', icon: 'warning', showCancelButton: true, background: '#0f172a', color: '#fff' })).isConfirmed) {
                fetch(`api/delete_vault.php?vid=${vid}`).then(() => location.reload());
            }
        }
    </script>
</body>
</html>

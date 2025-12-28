<?php
// api/get_github_files.php - INTELLIGENT SORTING & FILTERING
include '../includes/connection.php';

// Security Gate
if (!isset($_SESSION['user_id'])) { exit("ACCESS_DENIED"); }

function fetchGitHubFiles() {
    $token = GITHUB_PAT; 
    $owner = REPO_OWNER;
    $repo = REPO_NAME;
    
    // Target the ROOT folder
    $url = "https://api.github.com/repos/$owner/$repo/contents";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: PortalOS-Architect",
        "Authorization: token $token"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ['error' => true];
    }
    
    return json_decode($response, true);
}

$files = fetchGitHubFiles();

// 1. EXTENSION BLACKLIST (Files to IGNORE)
// Automatically ignores these extensions regardless of name
$ignored_extensions = ['html', 'js', 'py', 'css', 'php', 'md', 'json', 'gitignore', 'gitattributes', 'cname'];

// 2. INTELLIGENT CATEGORY MAPPING
// Format: 'Label' => [ 'regex_pattern', [keywords array] ]
// Regex '/cs[-_]?501/i' catches: cs501, CS501, Cs-501, CS_501
$category_rules = [
    'CS-501 (Theory of Computation)' => [
        'regex' => '/cs[-_]?501/i',
        'keywords' => ['toc', 'theory of computation', 'automata', 'computation', 'finite']
    ],
    'CS-502 (Database Management)' => [
        'regex' => '/cs[-_]?502/i',
        'keywords' => ['dbms', 'sql', 'database', 'rdbms', 'mysql', 'query']
    ],
    'CS-503 (Data Analytics & Cyber)' => [
        'regex' => '/cs[-_]?503/i',
        'keywords' => ['cyber', 'security', 'data', 'analytics', 'analysis', 'hacking', 'forensic']
    ],
    'CS-504 (Web Technology)' => [
        'regex' => '/cs[-_]?504/i',
        'keywords' => ['iwad', 'iwat', 'internet', 'web', 'development', 'frontend', 'backend']
    ]
];

// 3. HELPER: Get File Icon & Color
function getFileStyle($ext) {
    switch($ext) {
        case 'pdf': return ['icon' => 'file-text', 'color' => 'text-red-500', 'bg' => 'bg-red-500/10'];
        case 'docx': 
        case 'doc': return ['icon' => 'file-type-2', 'color' => 'text-blue-500', 'bg' => 'bg-blue-500/10'];
        case 'jpg':
        case 'jpeg':
        case 'png': return ['icon' => 'image', 'color' => 'text-purple-500', 'bg' => 'bg-purple-500/10'];
        case 'zip':
        case 'rar': return ['icon' => 'archive', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-500/10'];
        case 'ppt':
        case 'pptx': return ['icon' => 'presentation', 'color' => 'text-orange-500', 'bg' => 'bg-orange-500/10'];
        default: return ['icon' => 'file', 'color' => 'text-slate-500', 'bg' => 'bg-slate-500/10'];
    }
}

// ----------------------------------------------------
// PROCESS & RENDER
// ----------------------------------------------------

if (isset($files['error'])) {
    echo '<div class="text-center py-10 mono text-[10px] text-red-400">REPO_CONNECTION_FAILURE</div>';
} 
elseif (!empty($files) && is_array($files)) {
    
    // Prepare buckets
    $sorted = [
        'CS-501 (Theory of Computation)' => [],
        'CS-502 (Database Management)' => [],
        'CS-503 (Data Analytics & Cyber)' => [],
        'CS-504 (Web Technology)' => [],
        'General / Unknown' => []
    ];

    $has_valid_files = false;

    foreach ($files as $file) {
        // Skip directories
        if ($file['type'] !== 'file') continue;

        $name = htmlspecialchars($file['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        // FILTER: Check strict extension blacklist
        if (in_array($ext, $ignored_extensions)) continue;

        $has_valid_files = true;
        $name_lower = strtolower($name);
        $assigned = false;

        // MATCHING LOGIC
        foreach ($category_rules as $cat_name => $rules) {
            // 1. Check Regex (e.g., CS503, cs-503)
            if (preg_match($rules['regex'], $name)) {
                $sorted[$cat_name][] = $file;
                $assigned = true;
                break;
            }
            // 2. Check Keywords (e.g., "cyber security")
            foreach ($rules['keywords'] as $keyword) {
                if (strpos($name_lower, $keyword) !== false) {
                    $sorted[$cat_name][] = $file;
                    $assigned = true;
                    break 2;
                }
            }
        }

        if (!$assigned) {
            $sorted['General / Unknown'][] = $file;
        }
    }

    if (!$has_valid_files) {
         echo '<div class="text-center py-10 opacity-50 mono text-[10px] uppercase tracking-widest text-slate-400">
            No assignment documents found (System files hidden).
          </div>';
    } else {
        // RENDER THE LIST
        echo '<div class="space-y-8 p-2">';
        
        foreach ($sorted as $category => $categoryFiles) {
            if (empty($categoryFiles)) continue;

            echo '
            <div class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-500 mb-4 flex items-center gap-2 border-b border-white/5 pb-2">
                    <i data-lucide="folder-open" class="w-3 h-3"></i> '.$category.'
                </h3>
                <div class="space-y-3">';

            foreach ($categoryFiles as $file) {
                $name = htmlspecialchars($file['name']);
                $downloadUrl = $file['download_url']; 
                $size = round($file['size'] / 1024, 1) . ' KB';
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $style = getFileStyle($ext);

                echo '
                <div class="group flex justify-between items-center p-4 bg-white/5 border border-white/5 rounded-2xl cursor-pointer hover:bg-white/10 hover:border-blue-500/30 transition-all"
                     onclick="openMatrix(\''.$downloadUrl.'\', \''.$name.'\')">
                    <div class="flex items-center gap-4">
                        <div class="p-2 '.$style['bg'].' rounded-lg '.$style['color'].'">
                            <i data-lucide="'.$style['icon'].'" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-200 group-hover:text-white line-clamp-1">'.$name.'</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[9px] mono uppercase px-1.5 py-0.5 rounded bg-white/5 text-slate-400">'.$ext.'</span>
                                <span class="text-[9px] mono text-slate-500">'.$size.'</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 py-1 bg-blue-600 text-white text-[9px] font-bold rounded uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                        Read
                    </div>
                </div>';
            }
            echo '</div></div>';
        }
        echo '</div>';
    }
} else {
    echo '<div class="text-center py-10 opacity-50 mono text-[10px] uppercase tracking-widest text-red-400">REPO_EMPTY</div>';
}
?>
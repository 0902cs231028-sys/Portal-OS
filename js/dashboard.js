/**
 * AETHERIS CORE v2.0 - Dashboard Intelligence Engine
 * Specialized for RJIT Unit 2022-26 Reconnaissance
 */

const AetherisUI = {
    // 1. IDENTITY HUD: Master Notification System
    notify: (title, text, icon = 'info', timer = 4000) => {
        Swal.fire({
            title: title.toUpperCase(),
            text: text,
            icon: icon,
            background: '#020617', // Deep Space Background
            color: '#f8fafc',
            confirmButtonColor: '#3b82f6',
            showConfirmButton: timer === 0,
            timer: timer !== 0 ? timer : null,
            timerProgressBar: true,
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'border border-blue-500/30 rounded-2xl backdrop-blur-xl'
            }
        });
    },

    // 2. SYSTEM PULSE: Dynamic Element Transitions
    initEntrance: () => {
        // Entrance animation for glass-cards
        gsap.from(".glass-card, .stat-card", {
            opacity: 0,
            y: 20,
            stagger: 0.1,
            duration: 0.8,
            ease: "power4.out"
        });
    },

    // 3. TELEMETRY: Real-time Stat Updates
    updateStat: (elementId, newValue) => {
        const el = document.getElementById(elementId);
        if (el) {
            gsap.to(el, {
                innerText: newValue,
                duration: 2,
                snap: { innerText: 1 },
                ease: "expo.out"
            });
        }
    }
};

/**
 * ARCHITECT COMMANDS: Handlers for system events
 */

// Initialize OS when DOM is stabilized
document.addEventListener('DOMContentLoaded', () => {
    AetherisUI.initEntrance();
    
    // Check for session initiation flash
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('login_success')) {
        AetherisUI.notify('Handshake Verified', 'Sovereign intelligence link established.', 'success');
    }
});

// GLOBAL EXPOSURE: Links to PHP triggers
window.notify = AetherisUI.notify;
window.updateStat = AetherisUI.updateStat;

/**
 * RECONNAISSANCE SYNC: Manual Trigger for GitHub Matrix
 */
async function syncGitHubAssets() {
    AetherisUI.notify('Sync Initiated', 'Requesting stream from GitHub Matrix...', 'info');
    
    try {
        const response = await fetch('api/sync_github.php');
        const data = await response.json();
        
        if (data.status === 'success') {
            AetherisUI.notify('Sync Complete', `${data.files_count} Assets indexed successfully.`, 'success');
            // Dynamically refresh the Matrix list if it exists
            if (window.fetchMatrixData) fetchMatrixData(data.uid);
        }
    } catch (err) {
        AetherisUI.notify('Sync Error', 'Link to GitHub Matrix timed out.', 'error');
    }
}

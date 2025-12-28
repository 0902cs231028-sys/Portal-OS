/**
 * AETHERIS CORE - Private Infiltration & DM System
 * Status: Debug Mode Active
 */

// 1. Force-Bind the Submit Event (Event Delegation)
document.addEventListener('submit', async function(e) {
    // Check if the submitted form is our DM Form
    if (e.target && e.target.id === 'dmForm') {
        e.preventDefault(); // Stop page reload
        
        const dmInput = document.getElementById('dmInput');
        const targetInput = document.getElementById('dmTargetId');
        
        const msg = dmInput.value.trim();
        const targetId = targetInput.value;

        // DEBUG: Print to console to prove it fired
        console.log(" Transmission Initiated...");
        console.log(" -> Target ID:", targetId);
        console.log(" -> Message:", msg);

        if (!msg) return; // Don't send empty messages

        if (!targetId) {
            alert("SYSTEM ERROR: Target Node ID Missing. Close and reopen the chat.");
            return;
        }

        // Clear input immediately for better UX
        dmInput.value = '';

        try {
            // 2. Perform the Uplink
            const response = await fetch('api/send_dm.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `target_id=${targetId}&message=${encodeURIComponent(msg)}`
            });
            
            // Check if backend received it
            const result = await response.json();
            console.log(" -> Server Response:", result);

            if(result.status === 'success') {
                fetchDMs(targetId); // Refresh feed
            } else {
                alert("UPLINK FAILED: " + result.message);
            }
            
        } catch (err) { 
            console.error("NETWORK ERROR:", err);
        }
    }
});

let dmPollInterval = null;

// 3. Open Drawer Logic
window.openDM = (targetId, name, pic) => {
    console.log("Opening Secure Channel to User:", targetId);
    
    document.getElementById('dmTargetId').value = targetId;
    document.getElementById('dmTargetName').innerText = name.toUpperCase();
    document.getElementById('dmTargetPic').src = pic;
    
    document.getElementById('dmDrawer').classList.remove('translate-x-full');
    fetchDMs(targetId);
    
    if (dmPollInterval) clearInterval(dmPollInterval);
    dmPollInterval = setInterval(() => fetchDMs(targetId), 3000);
};

window.closeDM = () => {
    document.getElementById('dmDrawer').classList.add('translate-x-full');
    if (dmPollInterval) clearInterval(dmPollInterval);
};

async function fetchDMs(targetId) {
    try {
        const res = await fetch(`api/get_dms.php?target_id=${targetId}`);
        const html = await res.text();
        const feed = document.getElementById('dmFeed');
        feed.innerHTML = html;
        feed.scrollTop = feed.scrollHeight;
    } catch(e) {
        // Silent fail for polling
    }
}
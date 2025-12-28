/**
 * AETHERIS CORE - Global Network Controller
 */
const chatBox = document.getElementById('chatBox');
const chatForm = document.getElementById('chatForm');
const msgInput = document.getElementById('msgInput');

let shouldScroll = true;

// Auto-Scroll Logic
chatBox.addEventListener('scroll', () => {
    shouldScroll = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 20;
});

/**
 * Sync Protocol: Fetches the live transmission feed
 */
const fetchMessages = () => {
    fetch('api/get_messages.php')
        .then(res => {
            // Kill the silence: Check for HTTP errors
            if (!res.ok) throw new Error(`HTTP_STATUS_${res.status}`);
            return res.text();
        })
        .then(data => {
            // Update the HUD
            chatBox.innerHTML = data || '<div class="text-center opacity-20 text-[10px] mono uppercase tracking-[0.5em] mt-20">No_Active_Transmissions</div>';
            
            if (window.lucide) {
                lucide.createIcons();
            }

            if (shouldScroll) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        })
        .catch(err => {
            // Error Reporting: No more silent failures
            console.error("SYNC_CRITICAL:", err);
            chatBox.innerHTML = `<div class="text-center text-red-500/50 text-[10px] mono uppercase mt-10">Sync_Interrupted: ${err.message}</div>`;
        });
};

/**
 * Broadcast Protocol: Sends data to the Matrix
 */
if (chatForm) {
    chatForm.onsubmit = async (e) => {
        e.preventDefault();
        const msg = msgInput.value.trim();
        if (!msg) return;

        msgInput.value = ''; // Immediate UI Wipe

        try {
            const response = await fetch('api/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(msg)}`
            });

            if (!response.ok) throw new Error("TRANSMISSION_REJECTED");
            
            fetchMessages(); // Force immediate sync
        } catch (err) {
            console.error("BROADCAST_ERR:", err);
            alert("NETWORK_ERR: Message not delivered.");
        }
    };
}

/**
 * ARCHITECT ONLY: The Nuke Protocol
 */
window.nukeMessage = async (msgId) => {
    if (!confirm("TERMINATE_TRANSMISSION: Are you sure?")) return;

    const formData = new FormData();
    formData.append('action', 'delete_msg');
    formData.append('msg_id', msgId);

    try {
        const res = await fetch('api/nuke_handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
            fetchMessages(); 
        }
    } catch (err) {
        console.error("TERMINATION_FAILED:", err);
    }
};

// Polling interval for "Live" experience
setInterval(fetchMessages, 2000);
fetchMessages();

let peer = null;
let localStream = null;
let currentCallTarget = null;
let pollingActive = true;

// --- 1. INITIATE CALL (Caller Side) ---
window.initiateCall = async function(targetId) {
    try {
        console.log("Initializing Uplink to Node:", targetId);
        currentCallTarget = targetId;
        
        // 1. Get Microphone Access
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        
        // 2. Show HUD
        document.getElementById('activeCallHud').classList.remove('hidden');
        document.getElementById('activeCallTarget').innerText = "CONNECTING...";

        // 3. Create Peer (Initiator)
        peer = new SimplePeer({ initiator: true, trickle: false, stream: localStream });

        // 4. Generate Signal (Offer)
        peer.on('signal', data => {
            fetch('api/signaling_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=send_offer&to_user=${targetId}&sdp=${JSON.stringify(data)}`
            });
        });

        // 5. Handle Audio Stream
        peer.on('stream', stream => {
            const audio = document.getElementById('remoteAudio');
            if(audio) { audio.srcObject = stream; audio.play(); }
            document.getElementById('activeCallTarget').innerText = "AUDIO_LINK_ESTABLISHED";
        });

        peer.on('error', err => console.error("PEER_ERR:", err));

    } catch(err) {
        alert("Microphone Access Denied or Hardware Error");
        console.error(err);
    }
};

// --- 2. ACCEPT CALL (Receiver Side) ---
window.acceptCall = async function() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        
        // Hide Modal, Show HUD
        const modal = document.getElementById('incomingCallModal');
        if(modal) modal.classList.add('hidden');
        
        document.getElementById('activeCallHud').classList.remove('hidden');
        document.getElementById('activeCallTarget').innerText = "CONNECTING...";

        peer = new SimplePeer({ initiator: false, trickle: false, stream: localStream });

        peer.on('signal', data => {
            // Send Answer
            fetch('api/signaling_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=send_answer&to_user=${window.callerID}&sdp=${JSON.stringify(data)}`
            });
        });

        peer.on('stream', stream => {
            const audio = document.getElementById('remoteAudio');
            if(audio) { audio.srcObject = stream; audio.play(); }
            document.getElementById('activeCallTarget').innerText = "AUDIO_LINK_ESTABLISHED";
        });

        // Connect using the Offer stored in window
        peer.signal(JSON.parse(window.incomingSDP));

    } catch(err) {
        console.error("Accept Error:", err);
    }
};

// --- 3. SIGNAL POLLING (The Heartbeat) ---
async function pollSignals() {
    if (!pollingActive) return;

    try {
        const res = await fetch('api/signaling_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=check_signals'
        });
        const data = await res.json();

        // CASE A: Receiving an Offer (Phone Ringing)
        if (data.hasSignal && data.type === 'offer') {
            // If you have a modal, show it. If not, use confirm() as a backup.
            let accepted = false;
            const modal = document.getElementById('incomingCallModal');
            
            if (modal) {
                document.getElementById('callerName').innerText = data.from_name;
                modal.classList.remove('hidden');
                // We pause polling while the modal is open so it doesn't spam
                pollingActive = false;
            } else {
                // Fallback if modal HTML is missing
                accepted = confirm("Incoming P2P Connection from " + data.from_name + ". Accept?");
                if(accepted) {
                    window.incomingSDP = data.sdp;
                    window.callerID = data.from_id;
                    acceptCall();
                }
            }
            
            // Store data globally so acceptCall() can use it
            window.incomingSDP = data.sdp;
            window.callerID = data.from_id;
        }

        // CASE B: Receiving an Answer (Call Accepted)
        if (data.hasSignal && data.type === 'answer' && peer) {
            console.log("Connection Accepted by Peer");
            peer.signal(JSON.parse(data.sdp));
        }

    } catch (e) {
        // Silent fail
    } finally {
        if(pollingActive) setTimeout(pollSignals, 3000); // Check every 3s
    }
}

// Start the loop
pollSignals();

// --- 4. UTILS ---
window.endCall = function() {
    if(peer) peer.destroy();
    if(localStream) localStream.getTracks().forEach(track => track.stop());
    location.reload();
};

window.toggleMute = function() {
    if(localStream) {
        const track = localStream.getAudioTracks()[0];
        track.enabled = !track.enabled;
        // Visual feedback could go here
    }
};
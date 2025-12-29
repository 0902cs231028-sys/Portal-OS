// js/push_manager.js - SYSTEM DIAGNOSTIC MODE
// Reports exactly where the chain breaks: ServiceWorker -> API -> Browser -> Database

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function activateNeuralLink() {
    console.log("NEURAL_DIAGNOSTIC: Initiating...");

    // [CHECK 1] BROWSER COMPATIBILITY
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        alert("FAIL: This browser does not support Neural Links (Push API).");
        return;
    }

    try {
        // [CHECK 2] SERVICE WORKER REGISTRATION
        const reg = await navigator.serviceWorker.ready;
        if (!reg) throw new Error("Service Worker not active. Ensure sw.js is in the ROOT folder.");
        console.log("PASS 1/4: Service Worker Active");

        // [CHECK 3] FETCH VAPID KEY
        let publicKey;
        try {
            const keyResponse = await fetch('api/get_key.php');
            if (!keyResponse.ok) throw new Error(`HTTP Error ${keyResponse.status}`);
            const keyData = await keyResponse.json();
            publicKey = keyData.publicKey;
            if (!publicKey) throw new Error("Server returned empty key.");
            console.log("PASS 2/4: Key Acquired");
        } catch (e) {
            alert(`FAIL [Network]: Could not fetch VAPID Key. Check api/get_key.php.\n\nError: ${e.message}`);
            return;
        }

        // [CHECK 4] BROWSER SUBSCRIPTION (CRITICAL FAILURE POINT)
        let sub;
        try {
            // Unsubscribe first to clear bad states
            const oldSub = await reg.pushManager.getSubscription();
            if (oldSub) await oldSub.unsubscribe();

            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey)
            });
            console.log("PASS 3/4: Browser Accepted Handshake");
        } catch (e) {
            if (e.name === 'AbortError') {
                alert("FAIL [Brave/Network]: The browser blocked the connection to Google.\n\nSOLUTION:\n1. Disable Brave Shields (Lion Icon)\n2. Or use Chrome/Firefox.");
            } else if (e.name === 'NotAllowedError') {
                alert("FAIL [Permission]: You blocked notifications. Reset site permissions.");
            } else {
                alert(`FAIL [Subscription]: ${e.name} - ${e.message}`);
            }
            return;
        }

        // [CHECK 5] DATABASE SYNC
        try {
            const dbResponse = await fetch('api/save_subscription.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(sub)
            });
            
            const dbText = await dbResponse.text();
            let dbData;
            try {
                dbData = JSON.parse(dbText);
            } catch (jsonErr) {
                throw new Error("Invalid JSON response: " + dbText.substring(0, 100));
            }

            if (dbData.status === 'success') {
                console.log("PASS 4/4: Subscription Saved");
                alert("SUCCESS: System Fully Operational. Neural Link Established.");
            } else {
                throw new Error("Database Error: " + (dbData.message || "Unknown SQL error"));
            }

        } catch (e) {
            alert(`FAIL [Database]: Browser OK, but Server Save Failed.\n\nError: ${e.message}`);
            return;
        }

    } catch (e) {
        alert("CRITICAL SYSTEM FAILURE: " + e.message);
    }
}
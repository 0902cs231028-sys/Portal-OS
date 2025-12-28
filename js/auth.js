// js/auth.js
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            // STOP the page from reloading
            e.preventDefault(); 
            
            const email = document.getElementById('email').value;
            const btn = e.target.querySelector('button');
            const originalText = btn.innerHTML;

            // UI Feedback
            btn.innerHTML = "AUTHENTICATING...";
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('email', email);

                const response = await fetch('api/auth_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Access Denied');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert("Connection Error. Check if api/auth_handler.php exists.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    }
});

// Google Login Trigger
window.initiateGoogleLogin = function() {
    // This is the direct link to the Google Auth API
    // Replace YOUR_CLIENT_ID with the one from your Google Console
    const clientID = "YOUR_CLIENT_ID.apps.googleusercontent.com";
    const redirectURI = encodeURIComponent("https://shiroonigami23.free.nf/api/google_auth.php");
    const scope = encodeURIComponent("email profile");
    
    window.location.href = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${clientID}&redirect_uri=${redirectURI}&response_type=code&scope=${scope}&access_type=offline&prompt=consent`;
};

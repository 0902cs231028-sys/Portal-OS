/**
 * Aetheris Core - Profile Intelligence Engine
 */

// Image Compression & Preview
window.compressAndPreview = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (e) => {
        const img = new Image();
        img.src = e.target.result;
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 400; 
            const scaleSize = MAX_WIDTH / img.width;
            canvas.width = MAX_WIDTH;
            canvas.height = img.height * scaleSize;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById('profilePreview').src = dataUrl;
            window.compressedImage = dataUrl; // Full Base64 String
        };
    };
};

// Database Sync Protocol
window.saveProfile = async () => {
    const btn = document.getElementById('saveBtn');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = `<i data-lucide="loader-2" class="animate-spin w-4 h-4"></i> SYNCING...`;
    btn.disabled = true;

    // Constructing Multipart Form Data as expected by your PHP
    const formData = new FormData();
    formData.append('summary', document.getElementById('summary').value);
    formData.append('skills', document.getElementById('skills').value);
    formData.append('profile_img_base64', window.compressedImage || '');

    try {
        const response = await fetch('api/update_profile.php', {
            method: 'POST',
            body: formData // Sending as POST form data
        });

        const data = await response.json();

        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'IDENTITY_SYNCHRONIZED',
                text: 'Profile and Resume Matrix Updated.',
                background: '#020617',
                color: '#fff',
                timer: 2000,
                showConfirmButton: false
            });
        }
    } catch (err) {
        console.error("SYNC_ERROR:", err);
        Swal.fire({ icon: 'error', title: 'TRANSMISSION_FAILED', background: '#020617', color: '#ef4444' });
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
        lucide.createIcons();
    }
};

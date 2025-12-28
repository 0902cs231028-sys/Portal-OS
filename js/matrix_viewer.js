/**
 * AETHERIS CORE v2.0 - Apex Solution Matrix Engine
 * Specialized Integrated Reader for PDF/DOCX Intelligence
 */

const MatrixEngine = {
    viewer: document.getElementById('matrixViewer'),
    frame: document.getElementById('matrixFrame'),
    title: document.getElementById('matrixFileName'),
    download: document.getElementById('matrixDownload'),

    /**
     * INITIATE_SESSION: Open the Matrix stream with Path Normalization
     */
    open: function(fileUrl, fileName) {
        // 1. PATH_CALIBRATION: Ensure absolute URL for Google Proxy
        let absoluteUrl = fileUrl;
        if (!fileUrl.startsWith('http')) {
            const baseUrl = window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
            absoluteUrl = `${baseUrl}/${fileUrl}`;
        }

        // 2. HUD_INITIALIZATION
        this.title.innerText = `[RECON] SYNCING: ${fileName.toUpperCase()}...`;
        this.title.style.color = "#3b82f6"; // Blueprint blue
        this.download.href = absoluteUrl;
        
        // 3. PROXY_UPLINK: Use Google GView for high-grade document parsing
        const proxyUrl = `https://docs.google.com/gview?url=${encodeURIComponent(absoluteUrl)}&embedded=true`;
        
        // 4. SUPREME_TRANSITION: GSAP Powered Entrance
        this.viewer.classList.remove('hidden');
        gsap.fromTo(this.viewer, 
            { opacity: 0, scale: 1.1, backdropFilter: "blur(0px)" }, 
            { duration: 0.5, opacity: 1, scale: 1, backdropFilter: "blur(20px)", ease: "expo.out" }
        );
        
        // Lock OS scrolling to focus on intelligence
        document.body.style.overflow = 'hidden';

        // 5. STREAM_LOAD
        this.frame.src = proxyUrl;

        // TERMINAL_FEEDBACK: Glow emerald when stream is stable
        this.frame.onload = () => {
            this.title.innerText = fileName.toUpperCase();
            this.title.style.color = "#10b981"; // Emerald status
            this.title.classList.add('animate-pulse');
        };
    },

    /**
     * TERMINATE_SESSION: Close Matrix and clear cache
     */
    close: function() {
        gsap.to(this.viewer, { 
            duration: 0.4, 
            opacity: 0, 
            scale: 0.9, 
            ease: "power4.in",
            onComplete: () => {
                this.viewer.classList.add('hidden');
                this.frame.src = ""; // Flush buffer
                document.body.style.overflow = 'auto'; // Restore OS
                this.title.classList.remove('animate-pulse');
            }
        });
    }
};

// Global Exposure for OS Buttons
window.openMatrix = (url, name) => MatrixEngine.open(url, name);
window.closeMatrix = () => MatrixEngine.close();

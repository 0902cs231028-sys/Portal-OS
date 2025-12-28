```markdown
# 📜 Changelog

## [Auto-Log] - 2025-12-28
- **🤖 GitHub Actions:** ⚡ Update in `changelog.yml`

## [Auto-Log] - 2025-12-28
- **🗄️ Database:** ⚡ Update in `db.sql`

## [Auto-Log] - 2025-12-28
- **🤖 GitHub Actions:** 🎉 Created `changelog.yml`

All notable changes to the **Portal-OS** architecture will be documented here.

## [v2.2] - 2025-12-28 (Supreme Update)
### 🚀 Added
- **God Mode Admin Panel**: Full file system access (Edit/Delete/Rename) directly from the web UI.
- **Service Worker v2.2**: Fixed CORS issues with external CDNs and enabled background push notifications.
- **Apex Matrix**: Integrated GitHub API to fetch assignment solutions dynamically.
- **Smart Vault**: Auto-categorizes uploads based on filenames (e.g., "Cert" -> Certification).

### 🛠 Fixed
- **502 Bad Gateway**: Resolved session locking deadlock in `connection.php`.
- **P2P Signaling**: Fixed `initiateCall` reference error in `talkin.js`.
- **DM System**: Aligned database column names (`message_content` -> `message`) for instant transmission.

## [v2.0] - 2025-12-25 (Aetheris Core)
### ✨ Added
- **Glassmorphism UI**: Complete visual overhaul using TailwindCSS.
- **P2P Voice**: Added WebRTC audio calling.
- **Bounty Board**: Crowdsourced resource request system.

## [v1.0] - 2025-12-01 (Genesis)
- Initial release of student dashboard and login system.
# AI Agent Handover Document

**Welcome, Agent.** 
This document provides the critical architectural context, deployment pipeline specifics, and hard-learned quirks of this application. Read this carefully before suggesting large structural changes.

## 1. Core Stack
- **Framework:** Laravel 11
- **Frontend:** Livewire 3 + Alpine.js + Tailwind CSS + Vite
- **WebSockets:** Laravel Reverb
- **Database:** MySQL 8.0

## 2. Infrastructure & Deployment (Zero-Touch CI/CD)
The application runs on a local Linux-based machine but is fully containerized for production.
- **Docker Production:** Builds are managed via `Dockerfile.prod`.
- **CI/CD (GitHub Actions):** Pushing to `main` triggers `.github/workflows/docker-build-push.yml`. It builds a **multi-architecture** image (`linux/amd64`, `linux/arm64`) and pushes it to Docker Hub.
- **Auto-Deployment (Watchtower):** A Watchtower container (`containrrr/watchtower`) runs on the production machine. It polls Docker Hub every 60 seconds, automatically pulls new images, and restarts the containers.
- **Routing:** No ports are exposed to the host network. All traffic routes securely via a Cloudflare Tunnel (`cloudflared`) container.
- **Auto-Migrations:** The `Dockerfile.prod` uses a custom `start.sh` entrypoint script that automatically runs `php artisan migrate --force` *before* booting the web server on every deployment.

## 3. CRITICAL Quirks & "Do Not Touch" Rules

### A. The "JS Epoch" Time Architecture
This application features live-running clocks on the frontend (e.g., `GlobalTimer`). To keep the frontend Javascript (`Date.now()`) and backend perfectly synced across timezones:
- **Rule:** `started_at` and `stopped_at` in the `TimerLog` table are intentionally stored as **JavaScript Millisecond Epochs** (`bigInteger`), *not* standard MySQL `DATETIME`.
- **Rule:** In `app/Models/TimerLog.php`, these properties are cast as raw `integer`. **Do not cast them to Carbon objects in the `$casts` array**, or you will break the frontend JS math and Livewire subtraction logic.
- **Usage:** If you need a formatted date in a Blade view, use the pre-built Virtual Accessors: `$log->started_at_carbon->format(...)`.

### B. Vite Build Arguments in Docker
Because we intentionally ignore `.env` files during the Docker image build process for security, Vite does not have access to environment variables at build time.
- **Rule:** All public `VITE_*` variables (like Reverb keys/hosts) are passed directly into the GitHub Action as `build-args`.
- **Rule:** If you add a new `VITE_` variable to the application, you **MUST** map it in two places:
  1. `.github/workflows/docker-build-push.yml` (Under `build-args`)
  2. `Dockerfile.prod` (Using `ARG` and `ENV`)
  Failure to do this will result in Vite hardcoding blank strings into the production JS assets.

### C. Timezones
- The production containers run with `TZ=Asia/Kolkata` explicitly injected via `docker-compose.prod.yml` to ensure database records and PHP logic align perfectly.

---
**End of Handover.** You are now fully caught up on the architecture. Proceed with the user's requests!

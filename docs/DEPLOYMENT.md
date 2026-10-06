# Deployment Guide (GitHub Actions → Docker Hub → Server)

## How it works

```
git push main ──► GitHub Action ──► builds Dockerfile.prod (amd64 + arm64)
                                   └─► pushes  <DOCKER_USERNAME>/mm:latest  (+ :sha-xxxxxxx)
Server: Watchtower polls Docker Hub every 60s ─► pulls new :latest ─► restarts mm-prod & mm-prod-reverb
        start.sh runs `php artisan migrate --force` on every app start
```

The workflow triggers on pushes to `main` that touch `app/**`, `Dockerfile.prod`, `.dockerignore`
or the workflow file. It can also be run manually (Actions tab → *Run workflow*).

## 1. Docker Hub (one-time)

1. Create a repository named **`mm`** under your account (e.g. `iamjothees/mm`).
   `docker-compose.prod.yml` pulls `iamjothees/mm:latest`; change it if your username differs.
2. Account Settings → Security → **New Access Token** with *Read & Write* scope. Copy it.

## 2. GitHub repository (one-time)

1. Push this project to a GitHub repo (default branch `main`).
2. Settings → Secrets and variables → Actions → **New repository secret**:

   | Secret | Value |
   |---|---|
   | `DOCKER_USERNAME` | Docker Hub username |
   | `DOCKER_PASSWORD` | The access token from step 1 (not your password) |
   | `VITE_REVERB_APP_KEY` | Must equal `REVERB_APP_KEY` in the server `.env` (baked into the JS bundle) |

3. Settings → Actions → General → make sure Actions are enabled.
4. `VITE_REVERB_HOST` is hardcoded in the workflow (`ws-manage.byteandbrand.online`). Edit
   `.github/workflows/docker-build-push.yml` if your websocket hostname differs.
5. Go to the **Actions** tab → *Build and Push Docker Image* → **Run workflow** to do the first build.
   Confirm the image appears on Docker Hub.

> Changing any `VITE_*` value requires a new image build, not just a server restart.

## 3. Cloudflare Tunnel (one-time)

1. Cloudflare Zero Trust → Networks → Tunnels → create a tunnel, copy the **token**.
2. Add public hostnames (service URLs use compose service names):
   - `manage.example.com` → `http://app:80`
   - `ws-manage.example.com` → `http://reverb:8080`

## 4. Server setup (one-time)

Requires Docker + Docker Compose plugin.

```bash
mkdir -p ~/mm && cd ~/mm
# copy docker-compose.prod.yml here (rename not required; use -f below)
cp .env.example .env     # or create .env from the repo's .env.example, then edit
```

Edit `.env`:
- `TUNNEL_TOKEN`, `DB_ROOT_PASSWORD`, `DB_PASSWORD`, `APP_URL`, `REVERB_*`, `WATCHTOWER_WEBHOOK_URL`
- Keep `DB_HOST=db` (the compose service name).
- `REVERB_APP_KEY` must match the `VITE_REVERB_APP_KEY` GitHub secret.
- Generate `APP_KEY`:
  ```bash
  docker run --rm iamjothees/mm:latest php artisan key:generate --show
  ```
  Paste the result (`base64:...`) into `APP_KEY`.
- Set `WATCHTOWER_WEBHOOK_URL` to a real shoutrrr URL, or remove the `WATCHTOWER_NOTIFICATION*`
  lines from the compose file if you don't want notifications.

If the Docker Hub repo is **private**, log in on the server so Watchtower can pull
(it reads `~/.docker/config.json`):

```bash
docker login -u <DOCKER_USERNAME>
```

Start everything:

```bash
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml logs -f app
```

## 5. Day-to-day

- Deploy: merge/push to `main`. Wait for the Action to finish; Watchtower picks it up within ~1 minute.
- Manual update: `docker compose -f docker-compose.prod.yml pull && docker compose -f docker-compose.prod.yml up -d`
- Roll back: set the image tag in the compose file to a previous `sha-xxxxxxx` tag and `up -d`
  (also stop Watchtower or it will move you back to `latest`).
- Logs: `docker compose -f docker-compose.prod.yml logs -f app reverb`
- DB backup: `docker exec mm-prod-db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' > backup.sql`

## Troubleshooting

| Symptom | Cause / fix |
|---|---|
| Action fails at *Log in to Docker Hub* | `DOCKER_USERNAME` / `DOCKER_PASSWORD` secrets missing or token lacks write scope |
| App can't connect to DB | `DB_HOST` must be `db` (or `mm-prod-db`), not `mm-db` |
| Websocket won't connect | `VITE_REVERB_*` secrets/host differ from server `REVERB_*`; rebuild image after fixing |
| Watchtower can't pull | Private repo and no `docker login` on server |
| Build is slow | arm64 build runs under QEMU; drop `linux/arm64` from `platforms` if the server is amd64 |

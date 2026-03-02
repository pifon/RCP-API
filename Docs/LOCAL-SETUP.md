# Local setup: backend (RCP-API) + frontend (foodbook.uk)

RCP-API and foodbook.uk are **separate repositories**. Run each **from inside its own folder**, one by one. Foodbook defaults to **HTTPS port 8443** (so no conflict with RCP-API on 443); set `HTTPS_PORT=443` in foodbook’s `.env` only when the base domain is **foodbook.uk**.

Add to your **hosts** file (`/etc/hosts` on macOS/Linux, `C:\Windows\System32\drivers\etc\hosts` on Windows):

```
127.0.0.1 pifon foodbook
```

## Backend (RCP-API) – run from this repo

- **URL:** https://pifon/api  
- **.env:** `APP_URL=https://pifon` (used for nginx and self-signed cert)  
- **Ports:** 80, 443  

```bash
cd RCP-API
cp .env.example .env
# Set APP_URL=https://pifon and DB_*, MARIADB_*
docker compose up -d
# Open https://pifon/api (accept self-signed cert if needed)
```

## Frontend (foodbook.uk) – run from that repo

- **URL:** https://foodbook (via reverse-proxy on 443). For production **foodbook.uk** set `HTTPS_PORT=443` in `.env`.
- **.env:** `APP_URL=https://foodbook`, `API_BASE_URL=https://pifon/api`; for local proxy set `FOODBOOK_BIND=127.0.0.1`, `FOODBOOK_HTTP_PORT=8081`, `FOODBOOK_HTTPS_PORT=8444` (API uses 8080/8443)
- **Ports:** 8081→80, 8444→443 on host when using proxy (proxy binds 80/443)

```bash
cd foodbook.uk
cp .env.example .env
# Set APP_URL=https://foodbook, APP_BASE_URL=https://pifon/api (HTTPS_PORT defaults to 8443)
docker compose up -d
# Open https://foodbook (via reverse-proxy)
```

## Summary

| Repo      | Start from    | APP_URL           | APP_BASE_URL (foodbook only) | Ports      |
|-----------|---------------|-------------------|-------------------------------|------------|
| RCP-API   | `cd RCP-API`  | https://pifon     | –                             | 80, 443    |
| foodbook.uk | `cd foodbook.uk` | https://foodbook  | https://pifon/api             | 8081, 8444 (proxy uses 80, 443) |

**Production (foodbook.uk):** Set `APP_URL=https://foodbook.uk` and `HTTPS_PORT=443` in foodbook’s `.env` so the app listens on standard HTTPS.

Production: use real domains and set `LETSENCRYPT_EMAIL` for Let's Encrypt. Each repo is independent and can run on separate machines.

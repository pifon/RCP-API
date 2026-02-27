# Local setup: backend (RCP-API) + frontend (foodbook.uk)

RCP-API and foodbook.uk are **separate repositories**. Run each **from inside its own folder**, one by one. Foodbook defaults to **HTTPS port 8443** (so no conflict with RCP-API on 443); set `HTTPS_PORT=443` in foodbook’s `.env` only when the base domain is **foodbook.uk**.

Add to your **hosts** file (`/etc/hosts` on macOS/Linux, `C:\Windows\System32\drivers\etc\hosts` on Windows):

```
127.0.0.2 pifon
127.0.0.3 foodbook
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

- **URL:** https://foodbook:8443 (default; no port conflict with RCP-API). For production **foodbook.uk** set `HTTPS_PORT=443` in `.env`.
- **.env:** `APP_URL=https://foodbook`, `APP_BASE_URL=https://pifon/api` (leave `HTTPS_PORT` unset or `8443` for local)
- **Ports:** 8080→80, **8443**→443 (override with `HTTPS_PORT=443` when base domain is foodbook.uk)

```bash
cd foodbook.uk
cp .env.example .env
# Set APP_URL=https://foodbook, APP_BASE_URL=https://pifon/api (HTTPS_PORT defaults to 8443)
docker compose up -d
# Open https://foodbook:8443
```

## Summary

| Repo      | Start from    | APP_URL           | APP_BASE_URL (foodbook only) | Ports      |
|-----------|---------------|-------------------|-------------------------------|------------|
| RCP-API   | `cd RCP-API`  | https://pifon     | –                             | 80, 443    |
| foodbook.uk | `cd foodbook.uk` | https://foodbook  | https://pifon/api             | 8080, **8443** (or 443 if `HTTPS_PORT=443`) |

**Production (foodbook.uk):** Set `APP_URL=https://foodbook.uk` and `HTTPS_PORT=443` in foodbook’s `.env` so the app listens on standard HTTPS.

Production: use real domains and set `LETSENCRYPT_EMAIL` for Let's Encrypt. Each repo is independent and can run on separate machines.

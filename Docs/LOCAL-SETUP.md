# Local setup: backend (RCP-API) + frontend (foodbook.uk)

RCP-API and foodbook.uk are **separate repositories**. Run each **from inside its own folder**, one by one. Foodbook defaults to **HTTPS port 8443** (so no conflict with RCP-API on 443); set `HTTPS_PORT=443` in foodbook’s `.env` only when the base domain is **foodbook.uk**.

Add to your **hosts** file (`/etc/hosts` on macOS/Linux, `C:\Windows\System32\drivers\etc\hosts` on Windows):

```
127.0.0.1 pifon foodbook
```

## Backend (RCP-API) – run from this repo

- **URL:** https://pifon/api  
- **Preset:** `.env.pifon` (local) or `.env.pifon.co.uk` (production). Set `APP_KEY` in the preset or run `php artisan key:generate --show` and paste into `.env` after first `./up`.
- **Ports:** local 8080/8443; production 80/443  

```bash
cd RCP-API
# Set APP_KEY in .env.pifon (or in .env after first run)
./up pifon
# Open https://pifon/api (accept self-signed cert if needed)
```

## Frontend (foodbook.uk) – run from that repo

- **URL:** https://foodbook (via reverse-proxy on 443). For production **foodbook.uk** use `./up foodbook.uk`.
- **Preset:** `.env.foodbook` (local) or `.env.foodbook.uk` (production). `API_BASE_URL=https://pifon/api`; for local proxy preset uses ports 8081/8444 (API uses 8080/8443).
- **Ports:** 8081→80, 8444→443 on host when using proxy (proxy binds 80/443)

```bash
cd foodbook.uk
./up foodbook
# Open https://foodbook (via reverse-proxy)
```

## Summary

| Repo        | Start from      | Preset / APP_URL      | Ports (local)   |
|-------------|-----------------|------------------------|-----------------|
| RCP-API     | `cd RCP-API`    | `./up pifon`          | 8080, 8443      |
| foodbook.uk | `cd foodbook.uk`| `./up foodbook`        | 8081, 8444      |

**Production:** Use `./up pifon.co.uk` (API) and `./up foodbook.uk` (foodbook). Set `CERTBOT_EMAIL` / `LETSENCRYPT_EMAIL` in the production presets for Let's Encrypt. Each repo is independent and can run on separate machines.

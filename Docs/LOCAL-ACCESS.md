# Reaching the API (localhost / pifon / "cannot be reached")

The app container listens on **port 80 (HTTP)** and **443 (HTTPS)** and maps them to the host.

## From the **same machine** that runs Docker

- Use **HTTP** (not HTTPS) to avoid "connection is not secure" / certificate warnings:
  - **http://localhost/api**
  - **http://127.0.0.1/api** or **http://127.0.0.2/api** (if your hosts use 127.0.0.2)
- If you have **pifon** in your hosts file (`127.0.0.2 pifon`):
  - **http://pifon/api** or **http://127.0.0.2/api**

Using **https://** locally will show a certificate warning (self-signed cert). Prefer HTTP for local development.

## From **another device** (phone, another PC on your network)

- **localhost** and **pifon** (with hosts) only work on the machine where Docker is running.
- On the other device, use the **host machine’s IP** instead of localhost:
  - **http://\<host-ip\>/api**  
    Example: **http://192.168.1.100/api**

To find the host IP:

- **macOS:** `ipconfig getifaddr en0` (or the interface you use for Wi‑Fi/Ethernet)
- **Windows:** `ipconfig` and use the IPv4 address of the adapter you use for the network
- **Linux:** `hostname -I` or `ip addr`

## If you still get "cannot be reached"

1. **Confirm the stack is running**
   ```bash
   docker compose ps
   ```
   The `app` service should be **Up** and show `0.0.0.0:80->80/tcp`.

2. **Confirm nothing else is using port 80**
   - Another web server (Apache, nginx, IIS) or another container can bind port 80 and take traffic.
   - Stop other services using 80, or change the app port in `docker-compose.yml` (e.g. `"8080:80"`) and use **http://localhost:8080/api**.

3. **Test with curl from the host**
   ```bash
   curl -v http://127.0.0.2/api/
   ```
   You should see `HTTP/1.1 200 OK` and JSON. If this works but the browser does not, the problem is on the client (wrong URL, HTTPS instead of HTTP, or different machine).

4. **Check app logs**
   ```bash
   docker compose logs app --tail 50
   ```
   Look for nginx or PHP errors.

5. **Rebuild and restart**
   ```bash
   docker compose build app && docker compose up -d app
   ```

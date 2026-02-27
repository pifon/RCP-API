# Start Pifon API at boot

The stack uses `restart: unless-stopped`, so once Docker is running, containers will start or resume automatically. To have the **whole stack** start when the machine (or your user session) starts, use one of the following.

## macOS (launchd)

1. Edit `docker/com.pifon.api.plist`: replace **both** `ABSOLUTE_PATH_TO_REPO` with the full path to this repo, e.g.:
   ```
   /Users/yourname/Pifon/api
   ```

2. Install the Launch Agent:
   ```bash
   cp docker/com.pifon.api.plist ~/Library/LaunchAgents/
   launchctl load ~/Library/LaunchAgents/com.pifon.api.plist
   ```

3. The stack will start when you log in. Logs: `cat /tmp/com.pifon.api.log` and `/tmp/com.pifon.api.err`.

To disable:
```bash
launchctl unload ~/Library/LaunchAgents/com.pifon.api.plist
```

**Note:** Docker Desktop must be set to start when you sign in (Docker Desktop → Settings → General), otherwise the script may run before Docker is ready. If needed, add a short delay or start Docker first.

## Linux (systemd)

1. Edit `docker/pifon-api.service`: replace `ABSOLUTE_PATH_TO_REPO` with the full path to this repo.

2. Install and enable (run at boot):
   ```bash
   sudo cp docker/pifon-api.service /etc/systemd/system/
   sudo systemctl daemon-reload
   sudo systemctl enable pifon-api
   sudo systemctl start pifon-api
   ```

3. Useful commands:
   ```bash
   sudo systemctl status pifon-api
   sudo systemctl stop pifon-api
   sudo systemctl start pifon-api
   ```

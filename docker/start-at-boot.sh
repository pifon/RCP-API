#!/bin/sh
# Start Pifon API stack at boot/login. Used by launchd (macOS) or systemd (Linux).
cd "$(dirname "$0")/.." && exec docker compose up -d

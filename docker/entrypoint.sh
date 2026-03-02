#!/bin/sh
set -e

# Derive domain from APP_URL (from .env or env). Used for nginx server_name and cert paths.
if [ -n "$APP_URL" ]; then
    _url="$APP_URL"
else
    _url=$(grep -E '^APP_URL=' /var/www/html/.env 2>/dev/null | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'" || true)
fi
APP_DOMAIN=$(echo "$_url" | sed -e 's|https\?://||' -e 's|/.*||' -e 's|:.*||')
[ -z "$APP_DOMAIN" ] && APP_DOMAIN=pifon

# This is the API (pifon): never use foodbook.uk certificate.
if [ "$APP_DOMAIN" = "foodbook.uk" ]; then
    echo "APP_URL must not be foodbook.uk in the API project. Using pifon.co.uk for certificate."
    APP_DOMAIN=pifon.co.uk
fi

CERT_DIR="/etc/letsencrypt/live/$APP_DOMAIN"

# Obtain or renew valid Let's Encrypt certificate for pifon.co.uk on every docker compose up
# Requires: pifon.co.uk and www.pifon.co.uk DNS → this host, and port 80 reachable from the internet.
if [ "$APP_DOMAIN" = "pifon.co.uk" ] && [ -n "$CERTBOT_EMAIL" ]; then
    if [ ! -f "$CERT_DIR/fullchain.pem" ] || [ ! -f "$CERT_DIR/privkey.pem" ]; then
        echo "Obtaining Let's Encrypt certificate for pifon.co.uk..."
        if certbot certonly --standalone -d pifon.co.uk -d www.pifon.co.uk \
            --email "$CERTBOT_EMAIL" --agree-tos --non-interactive --preferred-challenges http; then
            echo "Certificate obtained successfully."
        else
            echo "Certbot failed. Ensure (1) pifon.co.uk and www.pifon.co.uk point to this host, (2) port 80 is open from the internet. Falling back to self-signed certificate."
            echo "To retry once DNS/80 are ready: docker exec api certbot certonly --webroot -w /var/www/html/certbot -d pifon.co.uk -d www.pifon.co.uk --email $CERTBOT_EMAIL --agree-tos --non-interactive && docker exec api nginx -s reload"
            mkdir -p "$CERT_DIR"
            openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
                -keyout "$CERT_DIR/privkey.pem" -out "$CERT_DIR/fullchain.pem" \
                -subj "/CN=$APP_DOMAIN" -addext "subjectAltName=DNS:$APP_DOMAIN,DNS:www.$APP_DOMAIN,DNS:localhost,DNS:pifon,DNS:127.0.0.1,IP:127.0.0.1" 2>/dev/null || true
        fi
    else
        # If existing cert is self-signed (from earlier fallback), obtain real Let's Encrypt cert instead of renew
        if openssl x509 -in "$CERT_DIR/fullchain.pem" -noout -issuer 2>/dev/null | grep -q "Let's Encrypt"; then
            echo "Checking certificate for pifon.co.uk (renew if due)..."
            certbot renew --standalone --non-interactive --preferred-challenges http || true
        else
            echo "Existing certificate is self-signed. Obtaining Let's Encrypt certificate for pifon.co.uk..."
            if certbot certonly --standalone -d pifon.co.uk -d www.pifon.co.uk \
                --email "$CERTBOT_EMAIL" --agree-tos --non-interactive --preferred-challenges http --force-renewal; then
                echo "Certificate obtained successfully."
            else
                echo "Certbot failed. Keeping self-signed certificate. Fix DNS and port 80, then retry (see message above)."
            fi
        fi
    fi
fi

# Generate self-signed certificate if it still doesn't exist (non-pifon.co.uk or certbot not configured)
if [ ! -f "$CERT_DIR/fullchain.pem" ] || [ ! -f "$CERT_DIR/privkey.pem" ]; then
    mkdir -p "$CERT_DIR"
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout "$CERT_DIR/privkey.pem" -out "$CERT_DIR/fullchain.pem" \
        -subj "/CN=$APP_DOMAIN" -addext "subjectAltName=DNS:$APP_DOMAIN,DNS:localhost,DNS:pifon,DNS:127.0.0.1,IP:127.0.0.1" 2>/dev/null || true
fi

# Generate nginx config from template
sed "s/__APP_DOMAIN__/$APP_DOMAIN/g" /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"

#!/bin/sh
# Renew Let's Encrypt certificates using webroot challenge
set -e
certbot renew --webroot -w /var/www/html/certbot --quiet
nginx -s reload 2>/dev/null || true

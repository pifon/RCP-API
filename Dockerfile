FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    certbot \
    cron \
    curl \
    nginx \
    openssl \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN rm -f /etc/nginx/sites-enabled/default
RUN mkdir -p /run/nginx /var/www/html/certbot /etc/letsencrypt/live

WORKDIR /var/www/html

# Copy nginx config and entrypoint
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template
COPY docker/nginx/generated.conf /etc/nginx/conf.d/generated.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/certbot-renew.sh /usr/local/bin/certbot-renew.sh
COPY docker/certbot-cron /etc/cron.d/certbot-renew

RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/certbot-renew.sh \
    && chmod 0644 /etc/cron.d/certbot-renew

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
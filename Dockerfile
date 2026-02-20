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
RUN mkdir -p /run/nginx /var/www/html/certbot

WORKDIR /var/www/html

COPY docker/nginx/ssl-redirect.conf /etc/nginx/conf.d/default.conf
COPY docker/certbot-renew.sh /usr/local/bin/certbot-renew.sh
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/certbot-cron /etc/cron.d/certbot-cron
COPY docker/supervisord.conf /etc/supervisord.conf

RUN chmod +x /usr/local/bin/certbot-renew.sh /usr/local/bin/entrypoint.sh \
    && chmod 0644 /etc/cron.d/certbot-cron \
    && crontab /etc/cron.d/certbot-cron

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

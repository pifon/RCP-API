FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    nginx \
    openssl \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN rm -f /etc/nginx/sites-enabled/default
RUN mkdir -p /run/nginx /etc/ssl/private

WORKDIR /var/www/html

COPY docker/nginx/ssl/pifon.crt /etc/ssl/certs/pifon.crt
COPY docker/nginx/ssl/pifon.key /etc/ssl/private/pifon.key
COPY docker/nginx/ssl-redirect.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80 443

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    zip \
    nginx \
    openssl \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*


# Remove the default server definition
RUN rm /etc/nginx/sites-enabled/default

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Run Composer
RUN composer install

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy the SSL certificates to the container
# COPY docker/nginx/ssl/pifon.crt /etc/ssl/certs/pifon.crt
# COPY docker/nginx/ssl/pifon.key /etc/ssl/private/pifon.key
COPY docker/nginx/ssl/live/pifon.co.uk/fullchain.pem /etc/ssl/certs/pifon.crt
COPY docker/nginx/ssl/live/pifon.co.uk/privkey.pem /etc/ssl/private/pifon.key

# Configure Nginx to use SSL
COPY docker/nginx/ssl-redirect.conf /etc/nginx/conf.d/default.conf

EXPOSE 80 443

# Start PHP-FPM and Nginx using supervisord
COPY docker/supervisord.conf /etc/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

# docker build -t pifon .
# docker run -d -p 80:80 -p 443:443 pifon

# Use official Php with Apache
FROM php:8.2-apache

# Install required Php extension for Laravel
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev zip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (needed for laravel routes)
RUN a2enmod rewrite

# Fix Apache ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set Apache DocumentRoot to /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Copy APP code
COPY . /var/www/html/

# Create uploads folder and set permissions
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# Set Working Dir
WORKDIR /var/www/html

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate Laravel application key if not exists
RUN if [ ! -f .env ]; then \
        cp .env.example .env; \
    fi && \
    php artisan key:generate --force

# Clear Laravel cache
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Render uses port 80, not 10000
EXPOSE 80

CMD ["apache2-foreground"]
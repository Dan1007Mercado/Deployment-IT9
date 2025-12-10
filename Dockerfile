# Use official PHP with Apache
FROM php:8.2-apache

# Install required PHP extensions for Laravel
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (needed for Laravel routes)
RUN a2enmod rewrite

# Set Apache DocumentRoot to Laravel's public folder
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Create uploads folder and set permissions
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Expose port 10000 (Render expects your app to listen here)
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]

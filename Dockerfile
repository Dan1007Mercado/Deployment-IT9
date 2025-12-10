# Use official Php with Apache
FROM php:8.2-apache

# Install required Php extension for Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# Install Node.js (needed for Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Enable Apache mod_rewrite (needed for laravel routes)
RUN a2enmod rewrite

# SET Apache DocumentRoot to /var/www/html/public 
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Set Working Dir
WORKDIR /var/www/html

# COPY APP code
COPY . /var/www/html/

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Install npm dependencies and build assets
RUN npm install && npm run build

# Create uploads folder and set permissions
RUN mkdir -p /var/www/html/public/uploads \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

# Set permission for laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose render's required port
EXPOSE 10000

CMD ["apache2-foreground"]
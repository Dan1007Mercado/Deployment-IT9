# Use official Php with Apache
FROM php:8.2-apache

# Install required PHP extensions for Laravel, Gmail API, and Stripe
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    # For dompdf (requires fontconfig)
    libfontconfig1 \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        zip \
        gd \
        mbstring \
        exif \
        pcntl \
        bcmath \
        ctype \
        fileinfo \
        json \
        tokenizer \
        xml \
        curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Fix Apache ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set Apache DocumentRoot to Laravel's public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first for better caching
COPY composer.json composer.lock* ./

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies (THIS WILL INSTALL STRIPE & GMAIL API)
RUN if [ -f composer.json ]; then \
        composer install --no-dev --optimize-autoloader --no-scripts --no-progress; \
    else \
        echo "No composer.json found"; \
        exit 1; \
    fi

# Copy the rest of the application
COPY . .

# Create necessary directories
RUN mkdir -p /var/www/html/public/uploads \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/public/uploads

# Generate Laravel application key
RUN if [ ! -f .env ]; then \
        if [ -f .env.example ]; then \
            cp .env.example .env; \
        else \
            echo "APP_KEY=" > .env; \
        fi \
    fi && \
    php artisan key:generate --force

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 80

CMD ["apache2-foreground"]
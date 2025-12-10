#Use official Php with Apache
FROM php:8.2-apache

#Install required Php extension for Laravel
RUN apt-get update && apt-get install -y \ git unzip libpq-dev zip \ && docker-php extension install pdo pdo_mysql pdo_psql zip

#Enable Apache mod_rewrite (needed for laravel routes)
RUN a2enmod rewrite

#SET Apache DocumentRoot ot /var/www/html/public 
RUN sed -i 's|/var/www/html/var/public|g' /etc/apache2/sites-available/000-default.conf\ 
&& sed -i 's|var/www/html|public|g' /etc/apache2.conf

#COPY APP code
COPY . /var/www/html/

#Create uploads folder and set permissions
RUN mkdir -p /var/www/html/public/uploads\ && chown -R www-data:www-data /var/www/html/public/uploads \ 
&& chmod -R 775 /var/www/html/public/uploads

#Set Working Dir
WORKDIR /var/www/html

#install composer //changes
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#install composer dependencies
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

#Expose render's required port
EXPOSE 10000

CMD ["apache2-foreground"]
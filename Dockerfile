# Menggunakan PHP dengan Apache sebagai base image
FROM php:8.1-apache

# Install ekstensi PHP yang diperlukan
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy semua file aplikasi ke direktori web Apache
COPY . /var/www/html/

# Set permission yang tepat
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
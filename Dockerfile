# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install zip mbstring bcmath pdo pdo_mysql

# Install MongoDB PHP Extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ensure the correct PHP extensions are loaded
RUN php -m

# Install dependencies with Composer
RUN composer install --no-dev --optimize-autoloader || cat /var/www/html/composer.lock

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]

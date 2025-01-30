# Use an official PHP image with Apache
FROM php:8.1-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install MongoDB PHP extension
RUN apt-get update && apt-get install -y libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Set working directory
WORKDIR /var/www/html

# Copy project files to the container
COPY . /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]

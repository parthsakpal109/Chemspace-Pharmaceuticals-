# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-mongodb

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]

#!/bin/sh
echo "Installing PHP 8.2..."

# Update package lists
apt-get update

# Install PHP 8.2 and required extensions
apt-get install -y php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-mongodb

# Ensure Composer installs dependencies correctly
composer install --no-dev --optimize-autoloader

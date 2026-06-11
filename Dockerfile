# Use official PHP-Apache image
FROM php:8.2-apache

# Install dependencies for Python and MySQL extensions
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    libmariadb-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

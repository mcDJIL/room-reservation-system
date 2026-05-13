FROM php:8.2-apache

# Install PHP extensions required for MySQL connection.
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite for cleaner URL handling if needed.
RUN a2enmod rewrite

WORKDIR /var/www/html

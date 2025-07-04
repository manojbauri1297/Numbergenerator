FROM php:8.2-apache

# Enable Apache rewrite if needed
RUN a2enmod rewrite

# Copy project files to the web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

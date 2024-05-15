# Use the official Apache image as base
FROM php:8.2.0-apache

# Enable Apache modules
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update \
    && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip \
    zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN docker-php-ext-install gettext intl pdo_mysql gd

# Configure GD extension
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Copy Apache virtual host configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Set the working directory
WORKDIR /var/www/html

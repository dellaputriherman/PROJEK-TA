FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    nano \
    libzip-dev \
    pkg-config \
    libssl-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql zip sockets


RUN pecl install mongodb-2.1.1 \
    && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 8100

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8100"]

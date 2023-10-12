FROM php:8.0-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application files to the container
COPY . .

# Install PHP dependencies
RUN composer install && \
    php artisan key:generate && \
    php artisan migrate:fresh --seed

CMD ["php-fpm"]

EXPOSE 9000

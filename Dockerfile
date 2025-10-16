# Imagen base
FROM php:8.3-fpm

# Directorio de trabajo
WORKDIR /var/www/desktimer

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar el proyecto
COPY . /var/www/desktimer

# Instalar dependencias Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data /var/www/desktimer
RUN chmod -R 775 /var/www/desktimer/storage /var/www/desktimer/bootstrap/cache

# Exponer puerto FPM
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]

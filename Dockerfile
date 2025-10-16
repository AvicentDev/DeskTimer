# Imagen base de PHP con Composer
FROM php:8.2-fpm

# Instala dependencias del sistema necesarias para Laravel
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Definir el directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Generar la clave de aplicación automáticamente
RUN php artisan key:generate

# Exponer el puerto que Render usa por defecto
EXPOSE 10000

# Comando de inicio (Render inyecta el puerto en $PORT)
CMD php artisan serve --host=0.0.0.0 --port=$PORT

# Imagen base con Apache
FROM php:8.3-apache

# Directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar proyecto
COPY . /var/www/html

# Instalar dependencias Laravel (sin dev)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Configurar DocumentRoot a /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto dinámico que Render asigna
EXPOSE 80

# Comando de inicio optimizado
# 1. Forzar a Apache a escuchar en $PORT
# 2. Arrancar Apache
# 3. Mantener el contenedor en foreground
CMD ["sh", "-c", "sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && apache2-foreground"]

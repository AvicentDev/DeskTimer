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

# Limpiar cachés de Laravel antes de hacer cache
RUN php artisan config:clear || true && \
    php artisan route:clear || true && \
    php artisan cache:clear || true && \
    php artisan view:clear || true

# Configurar DocumentRoot a /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto dinámico que Render asigna
EXPOSE 80

# Crear script de inicio
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "=== Iniciando DeskTimer Backend ==="\n\
\n\
# Configurar Apache para usar el puerto de Render\n\
echo "Configurando Apache para puerto $PORT"\n\
sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf\n\
sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Verificar conexión a base de datos\n\
echo "Verificando conexión a base de datos..."\n\
php artisan db:show || echo "Advertencia: No se pudo verificar la conexión a la BD"\n\
\n\
# IMPORTANTE: Limpiar cachés antes de optimizar\n\
echo "Limpiando cachés anteriores..."\n\
php artisan config:clear || true\n\
php artisan route:clear || true\n\
php artisan cache:clear || true\n\
php artisan view:clear || true\n\
\n\
# Optimizaciones de Laravel\n\
echo "Optimizando Laravel..."\n\
php artisan config:cache\n\
# NO hacer route:cache para evitar congelar rutas en Docker\n\
php artisan view:cache\n\
\n\
echo "=== Iniciando Apache ==="\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

# Variables de entorno para Apache
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV PORT=80

# Comando de inicio
CMD ["/start.sh"]

#!/usr/bin/env bash

# Script de build para Render
# Este script se ejecuta cada vez que se hace deploy

echo "🚀 Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader

echo "🔧 Limpiando cachés..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

echo "⚙️ Optimizando para producción..."
php artisan config:cache
php artisan route:cache

echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

echo "✅ Build completado!"

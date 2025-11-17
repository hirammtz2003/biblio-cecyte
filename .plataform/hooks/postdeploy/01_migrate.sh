#!/bin/bash
cd /var/app/current

# Esperar a que RDS esté listo
echo "Waiting for RDS to be ready..."
sleep 60

# Ejecutar migraciones
php artisan migrate --force

# Otros comandos de optimización
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Database migrations completed successfully!"
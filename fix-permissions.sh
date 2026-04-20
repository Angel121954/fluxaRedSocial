#!/bin/bash
# Fix file permissions for Laravel project

# Directorio del proyecto
PROJECT_DIR="/home/fluxa/projects/Fluxa"

cd "$PROJECT_DIR" || exit 1

echo "🔧 Fixing permissions..."

# Permisos para directorios Laravel
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Permisos para archivos
find storage -type f -exec chmod 664 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;

# Ownership (ajusta el usuario y grupo según tu sistema)
# Para Linux con usuario normal:
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache

# Permisos para artisan y ejecutables
chmod +x artisan
chmod +x bootstrap/*.php
chmod +x clear-cache.sh
chmod +x *.sh

echo "✅ Permissions fixed"
echo ""
echo "📝 If using Docker/Sail, also run:"
echo "   ./vendor/bin/sail artisan storage:link"
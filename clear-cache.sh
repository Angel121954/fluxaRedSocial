#!/bin/bash
# Clear all Laravel caches

./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan optimize:clear
./vendor/bin/sail artisan event:clear

rm -rf node_modules/.vite

# Si agregaste nuevas clases o packages
composer dump-autoload --optimize

echo "✅ All caches cleared"
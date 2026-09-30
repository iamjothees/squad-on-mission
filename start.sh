#!/bin/sh

echo "Running Database Migrations..."
php artisan migrate --force

echo "Starting Web Server..."
# Pass execution back to the base image's default startup script
exec /startup.sh

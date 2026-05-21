#!/bin/bash
# GetMyServe (GMS) - Setup Script
# Run this from the project directory: /var/www/projects/getmyserve

echo "=== GetMyServe Setup ==="

# Install dependencies
echo "[1/6] Installing Composer dependencies..."
composer install

# Generate app key
echo "[2/6] Generating application key..."
php artisan key:generate

# Create SQLite database
echo "[3/6] Creating SQLite database..."
touch database/database.sqlite

# Run migrations
echo "[4/6] Running migrations..."
php artisan migrate

# Seed database
echo "[5/6] Seeding database..."
php artisan db:seed

# Create storage link
echo "[6/6] Creating storage symlink..."
php artisan storage:link

echo ""
echo "=== Setup Complete! ==="
echo ""
echo "Default admin login:"
echo "  Email: admin@getmyserve.com"
echo "  Password: password"
echo ""
echo "Start the dev server:"
echo "  php artisan serve"
echo ""
echo "Then visit: http://localhost:8000"

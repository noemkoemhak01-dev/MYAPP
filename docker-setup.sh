#!/bin/bash

echo "🚀 Setting up Laravel API with Docker..."

# Create SQLite database file
touch database/database.sqlite

# Build and start containers
echo "📦 Building Docker containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "⏳ Waiting for containers to start..."
sleep 5

# Install dependencies
echo "📥 Installing Composer dependencies..."
docker-compose exec app composer install

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

# Set permissions
echo "🔒 Setting permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/bootstrap/cache

echo "✅ Setup complete!"
echo "🌐 API is running at: http://localhost:8000"
echo "📝 API endpoints available at: http://localhost:8000/api"
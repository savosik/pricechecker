#!/bin/bash

echo "🚀 Установка Laravel проекта..."

# Проверка наличия Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен. Пожалуйста, установите Docker."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose не установлен. Пожалуйста, установите Docker Compose."
    exit 1
fi

# Создание Laravel проекта, если его еще нет
if [ ! -f "composer.json" ]; then
    echo "📦 Создание нового Laravel проекта..."
    docker run --rm -v $(pwd):/app composer create-project laravel/laravel .
    
    # Удаление временных файлов
    rm -rf .git
fi

# Копирование .env файла, если его нет
if [ ! -f ".env" ]; then
    echo "📝 Создание .env файла..."
    cp .env.example .env
fi

# Запуск Docker контейнеров
echo "🐳 Запуск Docker контейнеров..."
docker-compose up -d

# Ожидание готовности MySQL
echo "⏳ Ожидание готовности MySQL..."
sleep 10

# Установка зависимостей
echo "📦 Установка зависимостей..."
docker-compose exec -T app composer install

# Генерация ключа приложения
echo "🔑 Генерация ключа приложения..."
docker-compose exec -T app php artisan key:generate

# Запуск миграций
echo "🗄️  Запуск миграций..."
docker-compose exec -T app php artisan migrate

echo "✅ Установка завершена!"
echo ""
echo "🌐 Приложение доступно по адресу: http://localhost:8080"
echo "🗄️  phpMyAdmin доступен по адресу: http://localhost:8081"
echo ""
echo "Полезные команды:"
echo "  docker-compose up -d          - Запустить контейнеры"
echo "  docker-compose down           - Остановить контейнеры"
echo "  docker-compose exec app bash  - Войти в контейнер приложения"
echo "  docker-compose logs -f app    - Просмотр логов приложения"




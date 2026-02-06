# Laravel + Docker проект

Проект Laravel с настройкой Docker для разработки.

## Требования

- Docker
- Docker Compose

## Быстрый старт

### Автоматическая установка

```bash
chmod +x install.sh
./install.sh
```

### Ручная установка

1. **Создание Laravel проекта** (если еще не создан):
```bash
docker run --rm -v $(pwd):/app composer create-project laravel/laravel .
```

2. **Настройка окружения**:
```bash
cp .env.example .env
```

3. **Запуск контейнеров**:
```bash
docker-compose up -d
```

4. **Установка зависимостей**:
```bash
docker-compose exec app composer install
```

5. **Генерация ключа приложения**:
```bash
docker-compose exec app php artisan key:generate
```

6. **Запуск миграций**:
```bash
docker-compose exec app php artisan migrate
```

## Доступ к сервисам

- **Приложение**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## Учетные данные БД

- **Хост**: mysql
- **Порт**: 3306
- **База данных**: laravel
- **Пользователь**: laravel
- **Пароль**: root

## Полезные команды

### Работа с контейнерами

```bash
# Запуск контейнеров
docker-compose up -d

# Остановка контейнеров
docker-compose down

# Просмотр логов
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql

# Перезапуск контейнера
docker-compose restart app
```

### Работа с Laravel

```bash
# Вход в контейнер приложения
docker-compose exec app bash

# Выполнение Artisan команд
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller ExampleController
docker-compose exec app php artisan cache:clear

# Установка пакетов через Composer
docker-compose exec app composer require package/name

# Установка пакетов через NPM
docker-compose exec app npm install
docker-compose exec app npm run dev
```

### Работа с базой данных

```bash
# Подключение к MySQL через контейнер
docker-compose exec mysql mysql -u laravel -proot laravel

# Импорт дампа
docker-compose exec -T mysql mysql -u laravel -proot laravel < dump.sql

# Экспорт дампа
docker-compose exec mysql mysqldump -u laravel -proot laravel > dump.sql
```

## Структура проекта

```
.
├── docker/
│   ├── nginx/
│   │   └── default.conf      # Конфигурация Nginx
│   └── php/
│       └── local.ini         # Настройки PHP
├── docker-compose.yml         # Конфигурация Docker Compose
├── Dockerfile                 # Образ PHP-FPM
├── .env.example               # Пример файла окружения
└── install.sh                # Скрипт установки
```

## Сервисы

- **app**: PHP 8.2-FPM с необходимыми расширениями
- **nginx**: Веб-сервер Nginx
- **mysql**: База данных MySQL 8.0
- **redis**: Кэш и очереди Redis
- **phpmyadmin**: Веб-интерфейс для управления БД

## Решение проблем

### Проблемы с правами доступа

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Очистка кэша

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Пересборка контейнеров

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```




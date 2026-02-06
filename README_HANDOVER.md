# Price Checker - Документация для передачи проекта

## 🖥️ Серверы и доступы

### Основной сервер
| Параметр | Значение |
|----------|----------|
| **IP** | `194.67.127.166` |
| **SSH User** | `root` |
| **SSH Password** | `UM441sFNfJDqjvfu` |
| **Хостинг** | reg.ru Cloud |

### VNC доступ (браузер для парсинга)
| Параметр | Значение |
|----------|----------|
| **noVNC (веб)** | http://194.67.127.166:6901 |
| **VNC напрямую** | `194.67.127.166:5901` |
| **Пароль** | `secret` |
| **Разрешение** | 1920x1080 |

---

## 📁 Структура проекта

**Путь на сервере:** `/var/www/mp_parse2`

### Docker контейнеры
```bash
docker ps
```

| Контейнер | Назначение | Порты |
|-----------|------------|-------|
| `mp_parse2_browser` | VNC браузер для парсинга | 5901, 6901 |
| `mp_parse2_nginx` | Веб-сервер | 80, 443 |
| `mp_parse2_app` | PHP-FPM приложение | 9000 |
| `mp_parse2_worker` | Queue worker | - |
| `mp_parse2_mysql` | База данных | 3306 |
| `mp_parse2_redis` | Кеширование | 6379 |
| `mp_parse2_phpmyadmin` | phpMyAdmin | 8081 |

---

## 🔐 База данных

| Параметр | Значение |
|----------|----------|
| **СУБД** | MySQL 8.0 |
| **Host** | `mysql` (внутри Docker) |
| **Database** | `laravel` |
| **Username** | `laravel` |
| **Password** | `root` |
| **phpMyAdmin** | http://194.67.127.166:8081 |

---

## 👤 Админ-панель (Moonshine)

| Параметр | Значение |
|----------|----------|
| **URL** | https://pricechecker.pecado.ru/admin |
| **Email** | `admin@example.com` |
| **Password** | `admin123` |

---

## 📧 Email (SMTP)

| Параметр | Значение |
|----------|----------|
| **Провайдер** | Yandex SMTP |
| **Host** | `smtp.yandex.ru` |
| **Port** | 587 |
| **Username** | `info@sex-opt.by` |
| **Password** | `tnhbavqhutmjmqfe` |
| **Encryption** | TLS |

---

## 🐙 Git репозиторий

| Параметр | Значение |
|----------|----------|
| **GitHub** | https://github.com/savosik/pricechecker |
| **SSH Clone** | `git@github.com:savosik/pricechecker.git` |
| **Ветка** | `main` |

### SSH ключ на сервере
Ключ расположен в `/root/.ssh/id_ed25519` и уже добавлен в GitHub.

---

## 🌐 Домен

| Параметр | Значение |
|----------|----------|
| **URL** | https://pricechecker.pecado.ru |
| **SSL** | Let's Encrypt (certbot) |

---

## ⚙️ .env (основные переменные)

```env
APP_NAME=Laravel
APP_ENV=local
APP_URL=https://pricechecker.pecado.ru

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=root

REDIS_HOST=redis

MAIL_HOST=smtp.yandex.ru
MAIL_USERNAME=info@sex-opt.by
MAIL_PASSWORD=tnhbavqhutmjmqfe

DOM_PARSER_API_TOKEN=ygjz5wuvD7BAZUWdK
```

---

## 🛠️ Полезные команды

### SSH подключение
```bash
ssh root@194.67.127.166
# Password: UM441sFNfJDqjvfu
```

### Docker
```bash
cd /var/www/mp_parse2

# Перезапуск всех контейнеров
docker-compose restart

# Логи приложения
docker-compose logs -f app

# Логи воркера
docker-compose logs -f worker

# Вход в контейнер
docker exec -it mp_parse2_app bash
```

### Laravel
```bash
docker exec -it mp_parse2_app php artisan cache:clear
docker exec -it mp_parse2_app php artisan queue:restart
docker exec -it mp_parse2_app php artisan migrate
```

### Git (на сервере)
```bash
cd /var/www/mp_parse2
git pull origin main
```

---

## 📋 Контакты

**Передал:** savosik  
**Дата:** 2026-02-06

# Production Deployment Guide

Complete guide for deploying mp_parse2 to production server 194.67.127.166 with domain pricechecker.pecado.ru.

## Prerequisites

### DNS Configuration
**CRITICAL**: Before deployment, configure your DNS:
```
Type: A Record
Name: pricechecker.pecado.ru
Value: 194.67.127.166
TTL: 3600 (or default)
```

Verify DNS propagation:
```bash
dig pricechecker.pecado.ru
nslookup pricechecker.pecado.ru
```

### Local Machine Requirements
- SSH access to 194.67.127.166
- `rsync` installed
- SSH keys configured (recommended) or password authentication

### Server Requirements
- Clean Ubuntu server at 194.67.127.166
- Root or sudo access
- Ports 22, 80, 443, 5901, 6901 accessible

---

## Deployment Steps

### Step 1: Prepare Local Environment

Make deployment scripts executable:
```bash
cd /home/savosik/projects/mp_parse2
chmod +x deploy.sh server-setup.sh init-ssl.sh
```

Test archive creation:
```bash
./deploy.sh --test-archive
```

### Step 2: Configure SSH Access

#### Option A: Using SSH Keys (Recommended)
```bash
# If you don't have SSH key
ssh-keygen -t rsa -b 4096

# Copy key to server
ssh-copy-id root@194.67.127.166
```

#### Option B: Using Password
Export your SSH username:
```bash
export DEPLOY_USER=root  # or your username
```

### Step 3: Deploy Project to Server

Run deployment script:
```bash
./deploy.sh
```

This will:
1. Create tar.gz archive of the project (including `.env`)
2. Transfer to server via SCP
3. Extract in `/var/www/mp_parse2`

### Step 4: Server Initial Setup

SSH to the server:
```bash
ssh root@194.67.127.166
cd /var/www/mp_parse2
```

Run server setup script:
```bash
chmod +x server-setup.sh
sudo bash server-setup.sh
```

This installs:
- Docker and Docker Compose
- Required system packages
- Configures UFW firewall
- Creates necessary directories

### Step 5: Configure Environment Variables

Review and update `.env` file on server:
```bash
nano .env
```

Ensure these are correctly set for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pricechecker.pecado.ru

# Database
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=<secure_password>

# Redis
REDIS_HOST=redis

# Mail configuration for notifications
MAIL_MAILER=smtp
MAIL_HOST=smtp.yandex.ru
MAIL_PORT=465
MAIL_USERNAME=<your_email>
MAIL_PASSWORD=<your_password>
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=<your_email>
```

### Step 6: Build and Start Docker Containers

Build production images:
```bash
docker compose -f docker-compose.prod.yml build --no-cache
```

Start database and redis first:
```bash
docker compose -f docker-compose.prod.yml up -d mysql redis
```

Wait for MySQL to be ready (~30 seconds):
```bash
docker logs -f mp_parse2_mysql
# Wait for "ready for connections"
```

Run database migrations:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

### Step 7: Initialize SSL Certificates

> **IMPORTANT**: Ensure DNS is propagated before this step!

Set your email for SSL notifications:
```bash
export SSL_EMAIL=your-email@example.com
```

Run SSL initialization:
```bash
chmod +x init-ssl.sh
bash init-ssl.sh
```

This will:
1. Start nginx temporarily
2. Request Let's Encrypt certificate
3. Configure nginx with SSL
4. Restart nginx with HTTPS enabled

### Step 8: Start All Services

Start remaining services:
```bash
docker compose -f docker-compose.prod.yml up -d
```

Verify all containers are running:
```bash
docker ps
```

Expected containers:
- `mp_parse2_app` - Laravel application
- `mp_parse2_worker` - Queue worker
- `mp_parse2_nginx` - Web server
- `mp_parse2_mysql` - Database
- `mp_parse2_redis` - Cache/Queue
- `mp_parse2_certbot` - SSL renewal
- `mp_parse2_browser` - VNC Chrome browser

### Step 9: Post-Deployment Setup

Generate application key if needed:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan key:generate
```

Clear and cache configuration:
```bash
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

Set storage permissions:
```bash
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/storage
docker compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/html/storage
```

---

## Verification

### 1. Test HTTPS Access
Open browser and navigate to:
```
https://pricechecker.pecado.ru
```

Verify:
- ✅ SSL certificate is valid (green padlock)
- ✅ Application loads without errors
- ✅ Assets load correctly

### 2. Test Admin Panel
```
https://pricechecker.pecado.ru/admin
```

Log in with your credentials and verify:
- ✅ Dashboard loads
- ✅ Resources (Products, Sellers, etc.) are accessible
- ✅ Database connectivity works

### 3. Test VNC Browser Access

#### Web Interface (noVNC):
```
http://194.67.127.166:6901
```

Or using SSH tunnel (more secure):
```bash
# On local machine
ssh -L 6901:localhost:6901 root@194.67.127.166
```

Then open: `http://localhost:6901`

Password: `secret`

#### Install Chrome Extension in VNC:
1. Connect to VNC
2. Open terminal in VNC
3. Install Chrome:
   ```bash
   wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
   sudo dpkg -i google-chrome-stable_current_amd64.deb
   sudo apt-get install -f -y
   ```
4. Open Chrome
5. Navigate to `chrome://extensions/`
6. Enable "Developer mode"
7. Click "Load unpacked"
8. Select `/headless/extension`

### 4. Check Docker Logs

```bash
# Application logs
docker logs mp_parse2_app --tail=50

# Nginx logs
docker logs mp_parse2_nginx --tail=50

# Worker logs
docker logs mp_parse2_worker --tail=50

# MySQL logs
docker logs mp_parse2_mysql --tail=50
```

### 5. Test Queue Worker

Check if worker is processing jobs:
```bash
docker logs -f mp_parse2_worker
```

Test by triggering a product parse in admin panel.

---

## Troubleshooting

### SSL Certificate Issues

**DNS not propagated:**
```bash
# Check DNS
dig pricechecker.pecado.ru

# Wait for propagation (can take up to 48 hours)
```

**Certificate request fails:**
```bash
# Check nginx logs
docker logs mp_parse2_nginx

# Verify port 80 is accessible
curl -I http://pricechecker.pecado.ru/.well-known/acme-challenge/test

# Check firewall
sudo ufw status
```

**Force certificate renewal:**
```bash
docker compose -f docker-compose.prod.yml run --rm certbot renew --force-renewal
docker compose -f docker-compose.prod.yml restart nginx
```

### Database Connection Issues

```bash
# Check MySQL is running
docker ps | grep mysql

# Test connection
docker compose -f docker-compose.prod.yml exec app php artisan tinker
# Then run: DB::connection()->getPdo();

# Check credentials in .env
cat .env | grep DB_
```

### Permission Issues

```bash
# Fix storage permissions
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/storage
docker compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/html/storage

# Fix bootstrap cache
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/html/bootstrap/cache
```

### VNC Not Accessible

```bash
# Check browser container
docker ps | grep browser
docker logs mp_parse2_browser

# Check firewall
sudo ufw status | grep 6901

# Allow VNC ports
sudo ufw allow 5901/tcp
sudo ufw allow 6901/tcp
sudo ufw reload
```

---

## Maintenance

### Update Application Code

```bash
# On local machine
cd /home/savosik/projects/mp_parse2
./deploy.sh

# On server
cd /var/www/mp_parse2
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml restart app worker
```

### View Logs

```bash
# All services
docker compose -f docker-compose.prod.yml logs -f

# Specific service
docker compose -f docker-compose.prod.yml logs -f app
```

### Backup Database

```bash
# Create backup
docker compose -f docker-compose.prod.yml exec mysql mysqldump -u laravel -p laravel > backup_$(date +%Y%m%d).sql

# Restore backup
docker compose -f docker-compose.prod.yml exec -T mysql mysql -u laravel -p laravel < backup_20260105.sql
```

### SSL Certificate Renewal

Certificates auto-renew via certbot container. To manually renew:
```bash
docker compose -f docker-compose.prod.yml run --rm certbot renew
docker compose -f docker-compose.prod.yml restart nginx
```

---

## Security Recommendations

### VNC Access
For production, restrict VNC access:

```bash
# Remove public VNC ports from docker-compose.prod.yml
# Access only via SSH tunnel:
ssh -L 6901:localhost:6901 root@194.67.127.166
```

### Firewall
Review and restrict access:
```bash
# Allow only specific IPs for VNC
sudo ufw delete allow 6901/tcp
sudo ufw allow from YOUR_IP_ADDRESS to any port 6901

# Same for port 5901
sudo ufw delete allow 5901/tcp
sudo ufw allow from YOUR_IP_ADDRESS to any port 5901
```

### Environment Variables
Ensure `.env` has strong passwords:
- Database password
- Redis password (if configured)
- Application key

---

## Quick Command Reference

```bash
# Start all services
docker compose -f docker-compose.prod.yml up -d

# Stop all services
docker compose -f docker-compose.prod.yml down

# Restart specific service
docker compose -f docker-compose.prod.yml restart app

# View logs
docker compose -f docker-compose.prod.yml logs -f app

# Execute artisan command
docker compose -f docker-compose.prod.yml exec app php artisan [command]

# Access app container shell
docker compose -f docker-compose.prod.yml exec app bash

# View all containers
docker ps -a

# Remove all stopped containers
docker system prune -a
```

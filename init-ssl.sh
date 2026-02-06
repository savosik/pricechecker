#!/bin/bash

# SSL Certificate Initialization Script
# Obtains Let's Encrypt certificates for pricechecker.pecado.ru

set -e

DOMAIN="pricechecker.pecado.ru"
EMAIL="${SSL_EMAIL:-admin@pecado.ru}"  # Change this to your email

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running in project directory
if [ ! -f "docker-compose.prod.yml" ]; then
    log_error "docker-compose.prod.yml not found. Run this script from project root."
    exit 1
fi

log_info "Initializing SSL certificates for ${DOMAIN}..."

# Create certificate directories
log_info "Creating certificate directories..."
mkdir -p docker/certbot/conf
mkdir -p docker/certbot/www

# Check if certificates already exist
if [ -d "docker/certbot/conf/live/${DOMAIN}" ]; then
    log_warn "Certificates already exist for ${DOMAIN}"
    read -p "Do you want to renew them? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "Skipping certificate generation"
        exit 0
    fi
fi

# Create temporary nginx configuration for certificate validation
log_info "Creating temporary nginx configuration..."
cat > docker/nginx/temp.conf << 'EOF'
server {
    listen 80;
    server_name pricechecker.pecado.ru;
    
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    location / {
        return 200 'OK';
        add_header Content-Type text/plain;
    }
}
EOF

# Start nginx with temporary configuration
log_info "Starting nginx for certificate validation..."
docker compose -f docker-compose.prod.yml up -d nginx

# Wait for nginx to start
sleep 5

# Request certificate
log_info "Requesting SSL certificate from Let's Encrypt..."
docker compose -f docker-compose.prod.yml run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email ${EMAIL} \
    --agree-tos \
    --no-eff-email \
    -d ${DOMAIN}

if [ $? -eq 0 ]; then
    log_info "${GREEN}SSL certificate obtained successfully!${NC}"
    
    # Restore production nginx configuration
    log_info "Applying production nginx configuration..."
    docker compose -f docker-compose.prod.yml stop nginx
    
    # Remove temporary config
    rm docker/nginx/temp.conf
    
    log_info "Restarting nginx with SSL configuration..."
    docker compose -f docker-compose.prod.yml up -d nginx
    
    log_info "${GREEN}SSL setup complete!${NC}"
    log_info "Your site should now be accessible at https://${DOMAIN}"
else
    log_error "Failed to obtain SSL certificate"
    log_error "Please check:"
    echo "  1. Domain ${DOMAIN} is pointing to this server"
    echo "  2. Ports 80 and 443 are not blocked by firewall"
    echo "  3. DNS propagation is complete (use: dig ${DOMAIN})"
    exit 1
fi

#!/bin/bash

# Server Setup Script for Ubuntu
# Installs Docker, Docker Compose, and configures firewall

set -e

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

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    log_error "Please run as root (use sudo)"
    exit 1
fi

log_info "Starting server setup..."

# Update system
log_info "Updating system packages..."
apt-get update
apt-get upgrade -y

# Install required packages
log_info "Installing required packages..."
apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release \
    software-properties-common \
    ufw \
    git

# Install Docker
log_info "Installing Docker..."
if ! command -v docker &> /dev/null; then
    # Add Docker's official GPG key
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
    chmod a+r /etc/apt/keyrings/docker.asc

    # Add Docker repository
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
      $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
      tee /etc/apt/sources.list.d/docker.list > /dev/null

    # Install Docker Engine
    apt-get update
    apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Start and enable Docker
    systemctl start docker
    systemctl enable docker

    log_info "Docker installed successfully"
else
    log_info "Docker already installed"
fi

# Verify Docker installation
docker --version
docker compose version

# Configure firewall
log_info "Configuring firewall (UFW)..."
ufw --force enable
ufw default deny incoming
ufw default allow outgoing

# Allow SSH
ufw allow 22/tcp comment 'SSH'

# Allow HTTP and HTTPS
ufw allow 80/tcp comment 'HTTP'
ufw allow 443/tcp comment 'HTTPS'

# Allow VNC ports (optional, can be restricted)
ufw allow 5901/tcp comment 'VNC'
ufw allow 6901/tcp comment 'noVNC'

# Reload firewall
ufw reload
ufw status

log_info "Firewall configured"

# Create necessary directories
log_info "Creating project directories..."
mkdir -p /var/www/mp_parse2/docker/certbot/conf
mkdir -p /var/www/mp_parse2/docker/certbot/www

# Set permissions
chown -R www-data:www-data /var/www/mp_parse2 || true

log_info "${GREEN}Server setup complete!${NC}"
log_info "Docker version: $(docker --version)"
log_info "Docker Compose version: $(docker compose version)"
log_info ""
log_info "Next steps:"
echo "  1. Ensure project files are in /var/www/mp_parse2"
echo "  2. Run SSL initialization: bash init-ssl.sh"
echo "  3. Start services: docker compose -f docker-compose.prod.yml up -d"

#!/bin/bash
# Script to install Google Chrome in the VNC container

set -e

echo "=== Installing Google Chrome ==="

# Add Google's signing key
wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -

# Add Chrome repository
echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google-chrome.list

# Update and install
apt-get update
apt-get install -y google-chrome-stable

# Clean up
apt-get clean
rm -rf /var/lib/apt/lists/*

echo "=== Google Chrome installed successfully ==="
google-chrome --version

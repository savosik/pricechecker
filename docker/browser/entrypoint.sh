#!/bin/bash
# Entrypoint script for browser container
# Installs Chrome, sets up extension with config, and starts VNC

set -e

echo "=== Browser Container Initialization ==="

# Use correct home directory for this image
HOME_DIR="/home/headless"

# Install Chrome if not already installed
if ! command -v google-chrome &> /dev/null; then
    echo "Installing Google Chrome..."
    /headless/scripts/install_chrome.sh
fi

# Copy extension to writable directory and generate config
EXTENSION_SRC="/headless/extension"
EXTENSION_DIR="$HOME_DIR/parser_extension"
CONFIG_TEMPLATE="$EXTENSION_SRC/config.template.json"
CONFIG_OUTPUT="$EXTENSION_DIR/config.json"

# Copy extension files to writable location
echo "Setting up extension..."
rm -rf "$EXTENSION_DIR"
cp -r "$EXTENSION_SRC" "$EXTENSION_DIR"

if [ -f "$CONFIG_TEMPLATE" ]; then
    echo "Generating extension config..."
    
    # Set defaults
    PARSER_API_URL="${PARSER_API_URL:-http://nginx}"
    PARSER_API_TOKEN="${PARSER_API_TOKEN:-}"
    PARSER_AUTO_START="${PARSER_AUTO_START:-false}"
    
    # Replace placeholders
    sed -e "s|\${PARSER_API_URL}|${PARSER_API_URL}|g" \
        -e "s|\${PARSER_API_TOKEN}|${PARSER_API_TOKEN}|g" \
        -e "s|\"\${PARSER_AUTO_START}\"|${PARSER_AUTO_START}|g" \
        "$CONFIG_TEMPLATE" > "$CONFIG_OUTPUT"
    
    echo "Config generated: $CONFIG_OUTPUT"
    cat "$CONFIG_OUTPUT"
fi

# Remove template from runtime extension
rm -f "$EXTENSION_DIR/config.template.json"

# Create Chrome profile directory
CHROME_PROFILE="$HOME_DIR/.config/google-chrome"
mkdir -p "$CHROME_PROFILE/Default/Extensions"

# Create desktop shortcut for Chrome with extension
DESKTOP_DIR="$HOME_DIR/Desktop"
mkdir -p "$DESKTOP_DIR"

cat > "$DESKTOP_DIR/Chrome-Parser.desktop" << EOF
[Desktop Entry]
Version=1.0
Type=Application
Name=Chrome Parser
Comment=Chrome with MP Parser Extension
Exec=google-chrome --no-sandbox --load-extension=${EXTENSION_DIR} --disable-infobars --start-maximized
Icon=google-chrome
Terminal=false
Categories=Network;WebBrowser;
EOF

chmod +x "$DESKTOP_DIR/Chrome-Parser.desktop"

# Create regular Chrome shortcut
cat > "$DESKTOP_DIR/Google-Chrome.desktop" << EOF
[Desktop Entry]
Version=1.0
Type=Application
Name=Google Chrome
Comment=Web Browser
Exec=google-chrome --no-sandbox --disable-infobars
Icon=google-chrome
Terminal=false
Categories=Network;WebBrowser;
EOF

chmod +x "$DESKTOP_DIR/Google-Chrome.desktop"

# Create symlink to extension folder
ln -sf "$EXTENSION_DIR" "$DESKTOP_DIR/chrome_extension"

# Fix ownership
chown -R headless:headless "$HOME_DIR/Desktop" "$EXTENSION_DIR" || true

echo "Desktop shortcuts and extension folder created"
echo "=== Initialization Complete ==="

# Execute original entrypoint
exec /dockerstartup/startup.sh "$@"

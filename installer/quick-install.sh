#!/bin/bash

# BillingHub - Quick Installation Script
# Usage: curl -fsSL https://raw.githubusercontent.com/yourusername/billinghub/main/installer/quick-install.sh | bash
# Or: wget -qO- https://raw.githubusercontent.com/yourusername/billinghub/main/installer/quick-install.sh | bash

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Config
GITHUB_USER="${GITHUB_USER:-yourusername}"
GITHUB_REPO="${GITHUB_REPO:-billinghub}"
INSTALL_DIR="${INSTALL_DIR:-.}"
VERSION="1.0.0"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║           🚀 BillingHub Quick Install v${VERSION}                      ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Check if running on Windows (Git Bash / WSL)
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" || "$OSTYPE" == "win32" ]]; then
    echo -e "${YELLOW}⚠️  Windows detected. Using Git Bash / WSL commands.${NC}"
    IS_WINDOWS=true
else
    IS_WINDOWS=false
fi

# Check prerequisites
echo -e "${BLUE}Checking prerequisites...${NC}"

# Check for git
if ! command -v git &> /dev/null; then
    echo -e "${RED}✗ Git is not installed. Please install Git first.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Git found${NC}"

# Check for PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP is not installed. Please install PHP 8.2 or higher.${NC}"
    exit 1
fi

PHP_VERSION=$(php -v | grep -oP 'PHP \K[0-9]+\.[0-9]+' | head -1)
if (( $(echo "$PHP_VERSION < 8.2" | bc -l) )); then
    echo -e "${RED}✗ PHP 8.2 or higher is required. Found: $PHP_VERSION${NC}"
    exit 1
fi
echo -e "${GREEN}✓ PHP $PHP_VERSION found${NC}"

# Check for Composer
if ! command -v composer &> /dev/null; then
    echo -e "${YELLOW}⚠️  Composer not found. Installing Composer...${NC}"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
fi
echo -e "${GREEN}✓ Composer found${NC}"

echo ""
echo -e "${BLUE}Downloading BillingHub...${NC}"

# Create installation directory if it doesn't exist
if [ ! -d "$INSTALL_DIR" ]; then
    mkdir -p "$INSTALL_DIR"
fi

cd "$INSTALL_DIR"

# Check if already cloned
if [ -d "billinghub/.git" ]; then
    echo -e "${YELLOW}Repository already exists. Updating...${NC}"
    cd billinghub
    git pull origin main
else
    # Clone the repository
    echo "Cloning from: https://github.com/$GITHUB_USER/$GITHUB_REPO.git"
    git clone --depth 1 https://github.com/$GITHUB_USER/$GITHUB_REPO.git
    cd billinghub
fi

echo -e "${GREEN}✓ Repository ready${NC}"

echo ""
echo -e "${BLUE}Installing dependencies...${NC}"

# Install PHP dependencies
if [ -f "composer.json" ]; then
    composer install --no-dev --optimize-autoloader
    echo -e "${GREEN}✓ PHP dependencies installed${NC}"
fi

# Install Node dependencies (if needed for assets)
if [ -f "package.json" ]; then
    if command -v npm &> /dev/null; then
        npm install
        npm run build
        echo -e "${GREEN}✓ Node dependencies installed${NC}"
    fi
fi

# Set permissions
echo -e "${BLUE}Setting permissions...${NC}"
chmod -R 755 storage bootstrap/cache
chmod -R 755 installer
echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}✓ Installation Complete!${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Determine how to open browser
if [ "$IS_WINDOWS" = true ]; then
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Start PHP server:"
    echo -e "   ${GREEN}php -S localhost:8000${NC}"
    echo ""
    echo "2. Open in browser:"
    echo -e "   ${GREEN}http://localhost:8000/installer/install.php${NC}"
else
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Start PHP server:"
    echo -e "   ${GREEN}php -S localhost:8000${NC}"
    echo ""
    echo "2. Open in browser:"
    echo -e "   ${GREEN}http://localhost:8000/installer/install.php${NC}"
    echo ""
    
    # Try to open browser automatically
    if command -v xdg-open &> /dev/null; then
        # Linux
        echo -e "${BLUE}Opening browser...${NC}"
        sleep 2 && xdg-open "http://localhost:8000/installer/install.php" &
        php -S localhost:8000
    elif command -v open &> /dev/null; then
        # macOS
        echo -e "${BLUE}Opening browser...${NC}"
        sleep 2 && open "http://localhost:8000/installer/install.php" &
        php -S localhost:8000
    else
        # Windows/WSL without xdg-open
        echo -e "${YELLOW}Please manually open:${NC}"
        echo -e "${GREEN}http://localhost:8000/installer/install.php${NC}"
        php -S localhost:8000
    fi
fi

echo ""
echo -e "${BLUE}📚 For production deployment, see: ./INSTALL_UBUNTU.md${NC}"
echo -e "${BLUE}📖 Full documentation available at: ./website/docs.html${NC}"
echo ""

#!/usr/bin/env bash
# MyWish.ma — Local Setup Script (PHP/MySQL)
# Sets up your local development environment.

set -e

# Colors
ORANGE='\033[38;5;208m'
GOLD='\033[38;5;220m'
GREEN='\033[0;32m'
RED='\033[0;31m'
GRAY='\033[0;90m'
NC='\033[0m'
BOLD='\033[1m'

# Banner
echo -e "${ORANGE}"
cat << "EOF"
   __  __    __        ___      __ 
  /  |/  /_ /_/ |     / (_)____/ /_
 / /|_/ / // /| | /| / / / ___/ __ \
/ /  / / // /_| |/ |/ / (__  ) / / /
/_/  /_/\___(_)__/|__/_/____/_/ /_/

EOF
echo -e "${GOLD}Local Setup Script · MyWish.ma (PHP/MySQL)${NC}"
echo -e "${GRAY}─────────────────────────────────────────────${NC}"
echo ""

# Step 1: Check PHP
echo -e "${BOLD}Step 1: Check PHP${NC}"
if ! command -v php &> /dev/null; then
  echo -e "${RED}✗ PHP is not installed.${NC}"
  echo "  Install from: https://www.php.net/downloads.php"
  echo "  Or use XAMPP/MAMP/Laragon"
  exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')
PHP_MAJOR=$(echo "$PHP_VERSION" | cut -d. -f1)
PHP_MINOR=$(echo "$PHP_VERSION" | cut -d. -f2)

if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]); then
  echo -e "${RED}✗ PHP 8.2+ required. Found: $PHP_VERSION${NC}"
  exit 1
fi
echo -e "${GREEN}✓ PHP $PHP_VERSION${NC}"

# Step 2: Check PHP extensions
echo ""
echo -e "${BOLD}Step 2: Check PHP extensions${NC}"
REQUIRED_EXTS=("pdo" "pdo_mysql" "mbstring" "json" "openssl" "curl")
MISSING_EXTS=()

for ext in "${REQUIRED_EXTS[@]}"; do
  if php -m | grep -qi "^$ext$"; then
    echo -e "${GREEN}  ✓ $ext${NC}"
  else
    echo -e "${RED}  ✗ $ext (missing)${NC}"
    MISSING_EXTS+=("$ext")
  fi
done

if [ ${#MISSING_EXTS[@]} -gt 0 ]; then
  echo -e "${RED}✗ Missing extensions. Install them and re-run.${NC}"
  exit 1
fi

# Step 3: Check MySQL
echo ""
echo -e "${BOLD}Step 3: Check MySQL${NC}"
if command -v mysql &> /dev/null; then
  MYSQL_VERSION=$(mysql --version | awk '{print $3}' | cut -d, -f1)
  echo -e "${GREEN}✓ MySQL client $MYSQL_VERSION${NC}"
else
  echo -e "${GRAY}○ MySQL client not found (optional — only needed for migrations via CLI)${NC}"
fi

# Step 4: Check Git
echo ""
echo -e "${BOLD}Step 4: Check Git${NC}"
if ! command -v git &> /dev/null; then
  echo -e "${RED}✗ Git is not installed.${NC}"
  exit 1
fi
GIT_VERSION=$(git --version | awk '{print $3}')
echo -e "${GREEN}✓ Git $GIT_VERSION${NC}"

# Step 5: Check Claude Code (optional)
echo ""
echo -e "${BOLD}Step 5: Check Claude Code (optional)${NC}"
if command -v claude &> /dev/null; then
  echo -e "${GREEN}✓ Claude Code installed${NC}"
else
  echo -e "${GRAY}○ Claude Code not installed (optional)${NC}"
  echo -e "${GRAY}  Install with: npm install -g @anthropic-ai/claude-code${NC}"
fi

# Step 6: Init Git repo if needed
echo ""
echo -e "${BOLD}Step 6: Git repository${NC}"
if [ ! -d ".git" ]; then
  echo -e "${GRAY}Initializing Git repository...${NC}"
  git init -b main
  echo -e "${GREEN}✓ Git initialized (main branch)${NC}"
else
  echo -e "${GREEN}✓ Git already initialized${NC}"
fi

# Step 7: Create .env from template
echo ""
echo -e "${BOLD}Step 7: Environment file${NC}"
if [ ! -f ".env" ]; then
  if [ -f ".env.example" ]; then
    cp .env.example .env

    # Generate APP_KEY
    APP_KEY=$(php -r "echo bin2hex(random_bytes(32));")
    if [ -n "$APP_KEY" ]; then
      # Use a portable sed (works on both macOS and Linux)
      if [[ "$OSTYPE" == "darwin"* ]]; then
        sed -i '' "s/^APP_KEY=$/APP_KEY=$APP_KEY/" .env
      else
        sed -i "s/^APP_KEY=$/APP_KEY=$APP_KEY/" .env
      fi
      echo -e "${GREEN}✓ .env created from .env.example${NC}"
      echo -e "${GREEN}✓ APP_KEY auto-generated${NC}"
    fi
    echo -e "${GRAY}  → Edit .env with your local DB credentials${NC}"
  else
    echo -e "${RED}✗ .env.example not found${NC}"
    exit 1
  fi
else
  echo -e "${GREEN}✓ .env already exists${NC}"
fi

# Step 8: Create storage directories
echo ""
echo -e "${BOLD}Step 8: Storage directories${NC}"
mkdir -p storage/logs storage/cache storage/uploads
touch storage/logs/.gitkeep storage/cache/.gitkeep storage/uploads/.gitkeep
chmod -R 755 storage/
echo -e "${GREEN}✓ storage/ directories ready (logs, cache, uploads)${NC}"

# Step 9: Make scripts executable
echo ""
echo -e "${BOLD}Step 9: Helper scripts${NC}"
chmod +x scripts/*.sh 2>/dev/null
echo -e "${GREEN}✓ Helper scripts are executable${NC}"

# Step 10: Display next steps
echo ""
echo -e "${GRAY}─────────────────────────────────────────────${NC}"
echo -e "${GREEN}${BOLD}✓ Setup complete!${NC}"
echo ""
echo -e "${BOLD}Next steps:${NC}"
echo ""
echo -e "  ${ORANGE}1.${NC} Edit ${GOLD}.env${NC} with your local DB credentials:"
echo -e "     ${GRAY}DB_HOST=127.0.0.1${NC}"
echo -e "     ${GRAY}DB_NAME=mywish_local${NC}"
echo -e "     ${GRAY}DB_USER=root${NC}"
echo -e "     ${GRAY}DB_PASS=...${NC}"
echo ""
echo -e "  ${ORANGE}2.${NC} Create the local database:"
echo -e "     ${GRAY}mysql -u root -p -e \"CREATE DATABASE mywish_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci\"${NC}"
echo ""
echo -e "  ${ORANGE}3.${NC} Apply migrations:"
echo -e "     ${GRAY}mysql -u root -p mywish_local < database/migrations/000_create_migrations_table.sql${NC}"
echo -e "     ${GRAY}mysql -u root -p mywish_local < database/migrations/001_create_users.sql${NC}"
echo -e "     ${GRAY}mysql -u root -p mywish_local < database/migrations/002_create_events.sql${NC}"
echo ""
echo -e "  ${ORANGE}4.${NC} Start the dev server:"
echo -e "     ${GRAY}php -S localhost:8000 -t public/${NC}"
echo ""
echo -e "  ${ORANGE}5.${NC} Open ${GOLD}http://localhost:8000${NC} in your browser"
echo ""
echo -e "  ${ORANGE}6.${NC} For cPanel deployment, see ${GOLD}SETUP-GUIDE.md${NC}"
echo ""
echo -e "  ${ORANGE}7.${NC} Start coding with Claude Code:"
echo -e "     ${GRAY}claude${NC}"
echo -e "     ${GRAY}> Read CLAUDE.md and let's start Sprint 1${NC}"
echo ""

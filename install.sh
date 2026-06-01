#!/bin/bash

# ╔══════════════════════════════════════════════════════════╗
# ║          AI Toolkit - Quick Installation Script          ║
# ╠══════════════════════════════════════════════════════════╣
# ║  Docker:  docker compose up -d && bash install.sh        ║
# ║  Manual:  bash install.sh                                ║
# ╚══════════════════════════════════════════════════════════╝

set -e

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}"
echo "╔══════════════════════════════════════╗"
echo "║     AI Toolkit - Installation        ║"
echo "╚══════════════════════════════════════╝"
echo -e "${NC}"

# ── Step 1: Environment ──
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ .env created${NC}"
else
    echo -e "${GREEN}✓ .env already exists${NC}"
fi

# ── Step 2: Composer ──
echo -e "${YELLOW}Installing PHP dependencies...${NC}"
composer install --no-interaction --optimize-autoloader
echo -e "${GREEN}✓ Composer dependencies installed${NC}"

# ── Step 3: App Key ──
echo -e "${YELLOW}Generating application key...${NC}"
php artisan key:generate --force
echo -e "${GREEN}✓ Application key generated${NC}"

# ── Step 4: Database ──
echo -e "${YELLOW}Running migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Database migrated${NC}"

# ── Step 5: Seed ──
echo -e "${YELLOW}Seeding database...${NC}"
php artisan db:seed --force
echo -e "${GREEN}✓ Database seeded${NC}"

# ── Step 6: Storage Link ──
echo -e "${YELLOW}Creating storage link...${NC}"
php artisan storage:link 2>/dev/null || true
echo -e "${GREEN}✓ Storage linked${NC}"

# ── Step 7: NPM ──
echo -e "${YELLOW}Installing frontend dependencies...${NC}"
npm install
echo -e "${GREEN}✓ NPM dependencies installed${NC}"

# ── Step 8: Build ──
echo -e "${YELLOW}Building frontend...${NC}"
npm run build
echo -e "${GREEN}✓ Frontend built${NC}"

# ── Step 9: Permissions ──
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo -e "${GREEN}╔══════════════════════════════════════╗"
echo "║     Installation Complete! 🎉        ║"
echo "╠══════════════════════════════════════╣"
echo "║                                      ║"
echo "║  1. Set OPENAI_API_KEY in .env       ║"
echo "║  2. Set STRIPE keys in .env          ║"
echo "║  3. Run: php artisan serve           ║"
echo "║  4. Visit: http://localhost:8000     ║"
echo "║                                      ║"
echo "║  Admin Login:                        ║"
echo "║  Email: admin@aitoolkit.com          ║"
echo "║  Pass:  password                     ║"
echo "║                                      ║"
echo -e "╚══════════════════════════════════════╝${NC}"

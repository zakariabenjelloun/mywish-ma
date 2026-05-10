#!/usr/bin/env bash
# pre-deploy-check.sh — Run safety checks before deploying
# Usage: ./scripts/pre-deploy-check.sh

set -e

# Colors
ORANGE='\033[38;5;208m'
GOLD='\033[38;5;220m'
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
GRAY='\033[0;90m'
NC='\033[0m'
BOLD='\033[1m'

ERRORS=0
WARNINGS=0

echo -e "${ORANGE}${BOLD}🔍 Pre-deploy checks${NC}"
echo -e "${GRAY}────────────────────${NC}"
echo ""

# Check 1: .env not in Git
echo -e "${BOLD}1. Checking .env is NOT staged in Git${NC}"
if git ls-files --error-unmatch .env &> /dev/null; then
  echo -e "${RED}  ✗ CRITICAL: .env is tracked by Git! Remove it immediately:${NC}"
  echo -e "${RED}     git rm --cached .env${NC}"
  ERRORS=$((ERRORS+1))
else
  echo -e "${GREEN}  ✓ .env is not in Git${NC}"
fi

# Check 2: No var_dump / dd / die in PHP files
echo ""
echo -e "${BOLD}2. Scanning for debug statements${NC}"
DEBUG_PATTERNS=("var_dump(" "dd(" "die(" "exit(" "print_r(")
DEBUG_FOUND=0

for pattern in "${DEBUG_PATTERNS[@]}"; do
  RESULTS=$(grep -rn --include="*.php" -F "$pattern" src/ public/ 2>/dev/null | grep -v "//.*$pattern" | grep -v "\.example" || true)
  if [ -n "$RESULTS" ]; then
    echo -e "${YELLOW}  ⚠ Found '$pattern':${NC}"
    echo "$RESULTS" | head -5 | sed 's/^/      /'
    DEBUG_FOUND=$((DEBUG_FOUND+1))
    WARNINGS=$((WARNINGS+1))
  fi
done

if [ "$DEBUG_FOUND" -eq 0 ]; then
  echo -e "${GREEN}  ✓ No debug statements found${NC}"
fi

# Check 3: No console.log in JS files
echo ""
echo -e "${BOLD}3. Scanning for console.log${NC}"
JS_RESULTS=$(grep -rn --include="*.js" -F "console.log" public/assets/js/ 2>/dev/null || true)
if [ -n "$JS_RESULTS" ]; then
  echo -e "${YELLOW}  ⚠ Found console.log:${NC}"
  echo "$JS_RESULTS" | head -5 | sed 's/^/      /'
  WARNINGS=$((WARNINGS+1))
else
  echo -e "${GREEN}  ✓ No console.log found${NC}"
fi

# Check 4: PHP syntax check on all .php files
echo ""
echo -e "${BOLD}4. PHP syntax check${NC}"
PHP_FILES=$(find src/ public/ -type f -name "*.php" 2>/dev/null)
SYNTAX_ERRORS=0

for file in $PHP_FILES; do
  if ! php -l "$file" > /dev/null 2>&1; then
    echo -e "${RED}  ✗ Syntax error in: $file${NC}"
    php -l "$file" | grep "error" | sed 's/^/      /'
    SYNTAX_ERRORS=$((SYNTAX_ERRORS+1))
  fi
done

if [ "$SYNTAX_ERRORS" -eq 0 ]; then
  PHP_COUNT=$(echo "$PHP_FILES" | wc -l)
  echo -e "${GREEN}  ✓ All $PHP_COUNT PHP files parsed OK${NC}"
else
  ERRORS=$((ERRORS+SYNTAX_ERRORS))
fi

# Check 5: Git status — uncommitted changes?
echo ""
echo -e "${BOLD}5. Git status${NC}"
UNCOMMITTED=$(git status --porcelain 2>/dev/null | wc -l | tr -d ' ')
if [ "$UNCOMMITTED" -gt 0 ]; then
  echo -e "${YELLOW}  ⚠ You have $UNCOMMITTED uncommitted change(s):${NC}"
  git status --short | head -10 | sed 's/^/      /'
  WARNINGS=$((WARNINGS+1))
else
  echo -e "${GREEN}  ✓ Working directory clean${NC}"
fi

# Check 6: Current branch
echo ""
echo -e "${BOLD}6. Current branch${NC}"
BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null)
echo -e "${GRAY}  Current branch: ${GOLD}$BRANCH${NC}"

if [ "$BRANCH" = "main" ]; then
  echo -e "${YELLOW}  ⚠ You are on 'main' — this will deploy to PRODUCTION${NC}"
  echo -e "${YELLOW}     Make sure all changes were tested on 'dev' first!${NC}"
fi

# Check 7: New migrations not committed
echo ""
echo -e "${BOLD}7. Checking for uncommitted migrations${NC}"
NEW_MIGRATIONS=$(git status --porcelain database/migrations/ 2>/dev/null | grep "\.sql$" || true)
if [ -n "$NEW_MIGRATIONS" ]; then
  echo -e "${YELLOW}  ⚠ Uncommitted migration files detected:${NC}"
  echo "$NEW_MIGRATIONS" | sed 's/^/      /'
  echo -e "${YELLOW}     Remember to apply them on the target DB after deploying!${NC}"
  WARNINGS=$((WARNINGS+1))
else
  echo -e "${GREEN}  ✓ No uncommitted migrations${NC}"
fi

# Check 8: APP_DEBUG check (only if .env exists locally)
echo ""
echo -e "${BOLD}8. Local .env sanity check${NC}"
if [ -f ".env" ]; then
  APP_ENV=$(grep "^APP_ENV=" .env | cut -d= -f2 | tr -d ' ')
  APP_DEBUG=$(grep "^APP_DEBUG=" .env | cut -d= -f2 | tr -d ' ')
  echo -e "${GRAY}  Local APP_ENV=$APP_ENV, APP_DEBUG=$APP_DEBUG${NC}"
  echo -e "${GRAY}  (This only affects YOUR local; the server has its own .env)${NC}"
else
  echo -e "${GRAY}  ○ No local .env found (normal if you haven't run setup-local.sh yet)${NC}"
fi

# ─────────────────────────────────────────────────
# Summary
# ─────────────────────────────────────────────────
echo ""
echo -e "${GRAY}────────────────────${NC}"
if [ "$ERRORS" -gt 0 ]; then
  echo -e "${RED}${BOLD}✗ $ERRORS critical error(s) found. DO NOT DEPLOY!${NC}"
  exit 1
elif [ "$WARNINGS" -gt 0 ]; then
  echo -e "${YELLOW}${BOLD}⚠ $WARNINGS warning(s) found. Review before deploying.${NC}"
  exit 0
else
  echo -e "${GREEN}${BOLD}✓ All checks passed. Safe to deploy!${NC}"
  echo ""
  echo -e "${ORANGE}Next:${NC}"
  echo -e "  ${GRAY}git push origin $BRANCH${NC}"
  echo -e "  ${GRAY}Then in cPanel: Update from Remote → Deploy HEAD Commit${NC}"
  exit 0
fi

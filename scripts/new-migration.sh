#!/usr/bin/env bash
# new-migration.sh — Create a new numbered SQL migration file
# Usage: ./scripts/new-migration.sh "description of change"
# Example: ./scripts/new-migration.sh "add phone verified to users"

set -e

# Colors
ORANGE='\033[38;5;208m'
GOLD='\033[38;5;220m'
GREEN='\033[0;32m'
RED='\033[0;31m'
GRAY='\033[0;90m'
NC='\033[0m'

MIGRATIONS_DIR="database/migrations"

if [ ! -d "$MIGRATIONS_DIR" ]; then
  echo -e "${RED}Error: $MIGRATIONS_DIR not found. Are you in the project root?${NC}"
  exit 1
fi

# Get description
if [ -z "$1" ]; then
  echo -e "${ORANGE}Enter migration description (e.g., 'add phone to users'):${NC}"
  read -r DESCRIPTION
else
  DESCRIPTION="$*"
fi

# Convert to snake_case
DESCRIPTION_SNAKE=$(echo "$DESCRIPTION" | tr '[:upper:]' '[:lower:]' | tr ' -' '__' | tr -cd '[:alnum:]_')

if [ -z "$DESCRIPTION_SNAKE" ]; then
  echo -e "${RED}Error: invalid description.${NC}"
  exit 1
fi

# Find the next migration number
LAST_NUM=$(ls "$MIGRATIONS_DIR"/*.sql 2>/dev/null \
  | sed -E 's|.*/([0-9]{3})_.*\.sql|\1|' \
  | sort -n \
  | tail -1)

if [ -z "$LAST_NUM" ]; then
  NEXT_NUM="000"
else
  NEXT_NUM=$(printf "%03d" $((10#$LAST_NUM + 1)))
fi

FILENAME="${NEXT_NUM}_${DESCRIPTION_SNAKE}.sql"
FILEPATH="$MIGRATIONS_DIR/$FILENAME"

# Create the migration file from template
cat > "$FILEPATH" <<EOF
-- ============================================================
-- Migration ${NEXT_NUM}: ${DESCRIPTION}
-- ============================================================
-- TODO: Describe what this migration does and why.
--
-- ROLLBACK: Document how to undo this migration.
-- ============================================================

-- TODO: Add your SQL here
-- Examples:
--   ALTER TABLE \`users\` ADD COLUMN \`new_field\` VARCHAR(255) NULL;
--   CREATE INDEX \`idx_new_field\` ON \`users\`(\`new_field\`);
--   CREATE TABLE IF NOT EXISTS \`new_table\` (...) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Track this migration (always last line — DO NOT REMOVE)
INSERT IGNORE INTO \`migrations\` (\`name\`) VALUES ('${NEXT_NUM}_${DESCRIPTION_SNAKE}');
EOF

echo -e "${GREEN}✓ New migration created: ${FILEPATH}${NC}"
echo ""
echo -e "${ORANGE}Next steps:${NC}"
echo -e "  ${GRAY}1.${NC} Edit the file: ${GOLD}${FILEPATH}${NC}"
echo -e "  ${GRAY}2.${NC} Test locally: ${GOLD}mysql -u root -p mywish_local < ${FILEPATH}${NC}"
echo -e "  ${GRAY}3.${NC} Verify it worked: ${GOLD}mysql -u root -p mywish_local -e \"SELECT * FROM migrations\"${NC}"
echo -e "  ${GRAY}4.${NC} Commit + push to dev: ${GOLD}git add ${FILEPATH} && git commit -m \"db: ${DESCRIPTION}\"${NC}"
echo -e "  ${GRAY}5.${NC} Apply on dev DB via cPanel phpMyAdmin BEFORE pushing to prod"
echo ""
echo -e "${GRAY}For full migration process: see docs/DATABASE.md${NC}"

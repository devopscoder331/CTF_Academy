#!/bin/bash

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "Environment HTB - CTF Challenge Test"
echo "=========================================="
echo ""

# Function to test endpoint
test_endpoint() {
    local url=$1
    local expected=$2
    local description=$3
    
    echo -n "Testing: $description ... "
    
    response=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null)
    
    if [ "$response" == "$expected" ]; then
        echo -e "${GREEN}✓ PASS${NC} (HTTP $response)"
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Expected: $expected, Got: $response)"
        return 1
    fi
}

# Function to test content
test_content() {
    local url=$1
    local search_string=$2
    local description=$3
    
    echo -n "Testing: $description ... "
    
    response=$(curl -s "$url" 2>/dev/null)
    
    if echo "$response" | grep -q "$search_string"; then
        echo -e "${GREEN}✓ PASS${NC} (Found: '$search_string')"
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Not found: '$search_string')"
        return 1
    fi
}

echo -e "${YELLOW}Application Tests (port 8010):${NC}"
echo "----------------------------------------"

# Check if container is running
if ! docker ps | grep -q environment_htb; then
    echo -e "${RED}✗ FAIL${NC} - Container 'environment_htb' is not running"
    echo "Start it with: make production"
    exit 1
fi

# Test endpoints
test_endpoint "http://localhost:8010/" "200" "Homepage accessible"
test_content "http://localhost:8010/" "Environment" "Homepage contains 'Environment'"
test_content "http://localhost:8010/" "Mailing List" "Homepage has mailing list"

test_endpoint "http://localhost:8010/login" "200" "Login page accessible"
test_content "http://localhost:8010/login" "Marketing Management Portal" "Login page correct"

test_endpoint "http://localhost:8010/management/dashboard" "302" "Dashboard redirects without auth"

# Test info page should redirect in production
test_endpoint "http://localhost:8010/management/info" "302" "/info redirects in production (correct!)"

echo ""
echo "=========================================="
echo "Testing database and migrations..."
echo "=========================================="

# Test database
echo -n "Checking database exists ... "
if docker exec environment_htb test -f /var/www/app/database/database.sqlite; then
    echo -e "${GREEN}✓ PASS${NC}"
else
    echo -e "${RED}✗ FAIL${NC}"
fi

# Test users table
echo -n "Checking users table ... "
if docker exec environment_htb php artisan tinker --execute="echo App\\Models\\User::count();" 2>/dev/null | grep -q "1"; then
    echo -e "${GREEN}✓ PASS${NC} (User exists)"
else
    echo -e "${YELLOW}⚠ WARNING${NC} (No users found, run: make seed)"
fi

echo ""
echo "=========================================="
echo "Container Status:"
echo "=========================================="
docker ps | grep environment_htb || echo "No containers running"

echo ""
echo "=========================================="
echo "Quick Commands:"
echo "=========================================="
echo "View logs:        make logs"
echo "Enter container:  make shell"
echo "Restart:          make restart"
echo "Clean rebuild:    make rebuild"
echo ""


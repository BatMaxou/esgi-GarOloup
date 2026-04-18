#!/bin/sh
set -eu

# Load Docker Swarm secrets and materialize them as Symfony env vars.
# We write .env.local instead of exporting, because php-fpm clears env vars
# for worker processes by default — dotenv is loaded per-request at runtime.

APP_SECRET=$(cat /run/secrets/garoloup_app_secret)
DB_PASSWORD=$(cat /run/secrets/garoloup_db_password)
MERCURE_JWT_SECRET=$(cat /run/secrets/garoloup_mercure_jwt)

cat > /srv/.env.local <<EOF
APP_SECRET=${APP_SECRET}
DATABASE_URL="mysql://garoloup:${DB_PASSWORD}@db:3306/garoloup?serverVersion=11.8.0-MariaDB&charset=utf8mb4"
MERCURE_JWT_SECRET=${MERCURE_JWT_SECRET}
EOF

# php-fpm workers run as www-data — make the file readable by that group only.
chgrp www-data /srv/.env.local
chmod 640 /srv/.env.local

exec docker-php-entrypoint "$@"

#!/bin/sh
set -eu

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:.' .env; then
    php artisan key:generate --force
fi

attempt=0
until php -r '
try {
    new PDO(
        "mysql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT").";dbname=".getenv("DB_DATABASE"),
        getenv("DB_USERNAME"),
        getenv("DB_PASSWORD")
    );
} catch (PDOException $exception) {
    exit(1);
}
'; do
    attempt=$((attempt + 1))
    if [ "$attempt" -ge 60 ]; then
        echo "Database did not become available after 60 seconds." >&2
        exit 1
    fi
    sleep 1
done

php artisan migrate --force --seed

exec "$@"

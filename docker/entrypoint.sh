#!/bin/sh
set -e

cd /var/www

if [ -z "$APP_KEY" ]; then
  echo "error: APP_KEY is not set. Add it in Railway Variables (php artisan key:generate --show)."
  exit 1
fi

php artisan storage:link 2>/dev/null || true

if [ "$RUN_MIGRATIONS" = "true" ]; then
  php artisan migrate --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"

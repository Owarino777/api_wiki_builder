#!/bin/sh
set -eu

read_secret() {
    key="$1"
    file_path="$2"

    if [ -n "${file_path}" ] && [ -f "${file_path}" ]; then
        export "${key}=$(cat "${file_path}")"
    fi
}

has_app_key() {
    if [ -n "${APP_KEY:-}" ]; then
        return 0
    fi

    if [ -f .env ] && grep -Eq '^APP_KEY=base64:' .env; then
        return 0
    fi

    return 1
}

read_secret "APP_KEY" "${APP_KEY_FILE:-}"
read_secret "DB_PASSWORD" "${DB_PASSWORD_FILE:-}"

if [ "${INSTALL_COMPOSER_DEPENDENCIES:-false}" = "true" ] && [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -f vendor/autoload.php ]; then
    echo "vendor/autoload.php is missing." >&2
    exit 1
fi

if ! has_app_key; then
    if [ "${GENERATE_APP_KEY:-false}" = "true" ] && [ -f .env ]; then
        php artisan key:generate --force --no-interaction
    else
        echo "APP_KEY is missing." >&2
        exit 1
    fi
fi

# In local/dev, clear cached package/provider manifests before running artisan commands.
# This avoids boot failures when cached manifests reference unavailable packages.
if [ "${APP_ENV:-production}" != "production" ]; then
    rm -f bootstrap/cache/*.php || true
    php artisan optimize:clear || true
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${SEED_DATABASE:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
fi

# Extract pre-bundled snapshot if available and not already extracted
SNAPSHOT_ZIP="/var/www/html/docker/app/bo7-snapshot.zip"
SNAPSHOT_DIR="/var/www/html/storage/app/private/codmunity"
if [ -f "${SNAPSHOT_ZIP}" ] && [ ! -f "${SNAPSHOT_DIR}/bo7-live-snapshot/manifest.json" ]; then
    echo "Extracting pre-bundled BO7 snapshot..."
    mkdir -p "${SNAPSHOT_DIR}"
    unzip -o "${SNAPSHOT_ZIP}" -d "${SNAPSHOT_DIR}" > /dev/null 2>&1 || true
    chown -R www-data:www-data "${SNAPSHOT_DIR}" || true
fi

if [ "${BOOTSTRAP_BO7_MODE:-skip}" != "skip" ]; then
    bootstrap_args="--mode=${BOOTSTRAP_BO7_MODE} --no-interaction"

    if [ -n "${BOOTSTRAP_BO7_SOURCE:-}" ]; then
        bootstrap_args="${bootstrap_args} --source=${BOOTSTRAP_BO7_SOURCE}"
    fi

    if [ "${BOOTSTRAP_BO7_FORCE:-false}" = "true" ]; then
        bootstrap_args="${bootstrap_args} --force"
    fi

    # Do not block php-fpm startup on a third-party dataset import in local/dev.
    if ! php artisan bo7:ensure-data ${bootstrap_args}; then
        if [ "${APP_ENV:-production}" = "production" ]; then
            exit 1
        fi

        echo "BO7 bootstrap failed during container startup; continuing without blocking php-fpm." >&2
    fi
fi

if [ "${APP_ENV:-production}" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan optimize:clear
fi

exec php-fpm

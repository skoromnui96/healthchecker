#!/usr/bin/env bash
set -e

TARGET_UID=$(stat -c "%u" /var/www/healthchecker)
TARGET_GID=$(stat -c "%g" /var/www/healthchecker)

if [ $TARGET_UID != 0 ] || [ $TARGET_GID != 0 ]; then
    echo '* Working around permission errors by making sure that "php-dev" has the same uid and gid as the host user'
fi

if [ $TARGET_UID != 0 ]; then
    echo ' -- Setting php-dev uid to '$TARGET_UID
    usermod -o -u $TARGET_UID php-dev || true
fi

if [ $TARGET_GID != 0 ]; then
    echo ' -- Setting php-dev gid to '$TARGET_GID
    groupmod -o -g $TARGET_GID php-dev || true
fi

php-cs-fixer self-update || true

exec "$@"

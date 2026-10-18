#!/bin/bash
# Runs on every container start, before Apache: on first boot it installs
# WordPress (so nobody else can claim the install screen), and on every boot it
# puts the theme from the image in place and activates it. Then it hands over
# to the official WordPress entrypoint.
set -euo pipefail

setup() {
  cd /var/www/html
  # the official entrypoint copies WordPress in and writes wp-config.php first
  until [ -f wp-includes/version.php ] && [ -f wp-config.php ]; do sleep 2; done

  # the theme baked into the image always wins, so each redeploy ships the latest version
  rm -rf wp-content/themes/vangel-naumovski
  cp -r /usr/src/vangel-naumovski wp-content/themes/vangel-naumovski
  chown -R www-data:www-data wp-content/themes/vangel-naumovski wp-content/uploads

  # wait for the database to accept connections
  until php -r '$m = @new mysqli(getenv("WORDPRESS_DB_HOST"), getenv("WORDPRESS_DB_USER"), getenv("WORDPRESS_DB_PASSWORD"), getenv("WORDPRESS_DB_NAME")); exit($m->connect_errno ? 1 : 0);'; do
    sleep 3
  done

  if ! wp --allow-root core is-installed >/dev/null 2>&1; then
    wp --allow-root core install \
      --url="http://localhost" \
      --title="${WP_SITE_TITLE:-48 Art Works}" \
      --admin_user="${WP_ADMIN_USER:-admin}" \
      --admin_password="${WP_ADMIN_PASSWORD:?set WP_ADMIN_PASSWORD in the stack environment}" \
      --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
      --skip-email
  fi
  wp --allow-root theme activate vangel-naumovski
  echo "reporun-init: WordPress ready, theme vangel-naumovski active"
}

setup > /proc/1/fd/1 2>&1 &
exec docker-entrypoint.sh "$@"

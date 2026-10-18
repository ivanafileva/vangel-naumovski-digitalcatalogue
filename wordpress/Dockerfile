FROM wordpress:6-php8.3-apache

# WP-CLI, used on start-up to install WordPress and activate the theme
RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
 && chmod +x /usr/local/bin/wp

COPY theme/vangel-naumovski /usr/src/vangel-naumovski
COPY reporun-init.sh /usr/local/bin/reporun-init.sh
RUN chmod +x /usr/local/bin/reporun-init.sh

ENTRYPOINT ["reporun-init.sh"]
CMD ["apache2-foreground"]

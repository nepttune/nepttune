#!/usr/bin/env bash

cd /var/www/html

if [ ! -h node_modules ]
then
    printf '\nCreating node_modules symlink\n'̈́
    ln -s www/node_modules node_modules
fi

printf '\nRunning Composer\n'
export COMPOSER_ALLOW_SUPERUSER=1
composer update --no-interaction

printf '\nRunning NPM\n'
npm update
chmod +x /var/www/html/node_modules/uglify-es/bin/uglifyjs

printf '\nCompiling SCSS\n'
sh /usr/local/bin/scss.sh

printf '\nMinifying JS\n'
sh /usr/local/bin/js.sh

if [ ! -f /etc/ssl/private/server.key ] || [ ! -f /etc/ssl/certs/server.crt ]
then
    printf '\nGenerating SSL certificate\n'
    openssl req \
        -newkey rsa:4096 -nodes -sha256 -keyout /etc/ssl/private/server.key \
        -x509 -days 365 -out /etc/ssl/certs/server.crt \
        -subj "/C=CZ/ST=Czech Republic/L=Peldax/O=Peldax/OU=Peldax/CN=localhost.com"
fi

printf '\nFixing permissions\n'
chown -R :www-data /var/log/apache2
chown -R :www-data /var/www/html
find /var/www/html \( -type f -execdir chmod 660 {} \; \) \
                -o \( -type d -execdir chmod 770 {} \; \)

printf '\nStarting Apache\n'
apache2ctl -D FOREGROUND

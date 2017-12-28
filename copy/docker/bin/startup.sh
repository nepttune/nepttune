#!/usr/bin/env bash

cd /var/www/html

if [ ! -h node_modules ]
then
    printf '\n---> Creating node_modules symlink\n\n'̈́
    ln -s www/node_modules node_modules
fi

printf '\n---> Running Composer\n\n'
export COMPOSER_ALLOW_SUPERUSER=1
composer update --no-interaction

printf '\n---> Running NPM\n\n'
npm update
chmod +x /var/www/html/node_modules/uglify-es/bin/uglifyjs

printf '\n---> Compiling SCSS\n\n'
sh /usr/local/bin/scss.sh

printf '\n---> Minifying JS\n\n'
sh /usr/local/bin/js.sh

if [ ! -f /etc/ssl/private/server.key ] || [ ! -f /etc/ssl/certs/server.crt ]
then
    printf '\n---> Generating SSL certificate\n\n'
    openssl req \
        -newkey rsa:4096 -nodes -sha256 -keyout /etc/ssl/private/server.key \
        -x509 -days 365 -out /etc/ssl/certs/server.crt \
        -subj "/C=CZ/ST=Czech Republic/L=Peldax/O=Peldax/OU=Peldax/CN=localhost.com"
fi

printf '\n---> Fixing permissions\n\n'
chown -R :www-data /var/log/apache2
chown -R 1000:www-data /var/www/html
find /var/www/html \( -type f -execdir chmod 660 {} \; \) \
                -o \( -type d -execdir chmod 770 {} \; \)

printf '\n---> Starting Apache\n\n'
apache2ctl -D FOREGROUND

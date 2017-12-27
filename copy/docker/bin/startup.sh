#!/usr/bin/env bash

cd /var/www/html

if [ ! -e node_modules ]
then
    echo 'Create symlink'̈́
    ln -s www/node_modules node_modules
fi

echo 'Composer'
export COMPOSER_ALLOW_SUPERUSER=1
composer update --no-interaction &> /dev/null

echo 'NPM'
npm update &> /dev/null

echo 'SCSS'
sh /usr/local/bin/scss.sh

echo 'JS'
sh /usr/local/bin/js.sh

if [ ! -e /etc/ssl/private/server.key ] || [ ! -e /etc/ssl/certs/server.crt ]
then
    echo 'Generate SSL certificate'
    openssl req \
        -newkey rsa:4096 -nodes -sha256 -keyout /etc/ssl/private/server.key \
        -x509 -days 365 -out /etc/ssl/certs/server.crt \
        -subj "/C=CZ/ST=Czech Republic/L=Peldax/O=Peldax/OU=Peldax/CN=localhost.com"
fi

chown -R :www-data /var/log/apache2
chown -R :www-data /var/www/html
find /var/www/html \( -type f -execdir chmod 660 {} \; \) \
                -o \( -type d -execdir chmod 770 {} \; \)

echo 'Apache start'
apache2ctl -D FOREGROUND

#!/usr/bin/env bash

cd /var/www/html

if [ ! -e node_modules ]; then
    ln -s www/node_modules node_modules
fi

composer update --no-interaction
npm update

sh /usr/local/bin/scss.sh
sh /usr/local/bin/js.sh

chown -R www-data /var/log/apache2
chown -R www-data /var/www/html

apache2ctl -D FOREGROUND

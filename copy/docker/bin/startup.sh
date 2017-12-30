#!/usr/bin/env bash

cd /var/www/html

if [ ! -h node_modules ]
then
    printf '\n---> Creating node_modules symlink\n\n'̈́
    ln -s www/node_modules node_modules
else
    printf '\n---> node_modules symlink already exists\n\n'̈́
fi

if [ ! -f /etc/ssl/private/server.key ] || [ ! -f /etc/ssl/certs/server.crt ]
then
    printf '\n---> Generating SSL certificate\n\n'
    openssl req \
        -newkey rsa:4096 -nodes -sha256 -keyout /etc/ssl/private/server.key \
        -x509 -days 365 -out /etc/ssl/certs/server.crt \
        -subj "/C=CZ/ST=Czech Republic/L=Peldax/O=Peldax/OU=Peldax/CN=localhost.com"
else
    printf '\n---> SSL certificate already exists\n\n'̈́
fi

printf '\n---> Running Composer\n\n'
sh /usr/local/bin/composer.sh

printf '\n---> Running NPM\n\n'
sh /usr/local/bin/npm.sh

printf '\n---> Compiling SCSS\n\n'
sh /usr/local/bin/scss.sh

printf '\n---> Minifying JS\n\n'
sh /usr/local/bin/js.sh

printf '\n---> Fixing permissions\n\n'
sh /usr/local/bin/permission.sh

printf '\n---> Starting Apache\n\n'
apache2ctl -D FOREGROUND

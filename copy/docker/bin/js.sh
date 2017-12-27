#!/usr/bin/env bash

for FILE in `find /var/www/html/www -type f -name '*.js' -not -name '*.min.js'`
do
    /var/www/html/node_modules/.bin/uglifyjs "${FILE}" > "${FILE%.js}.min.js"
done

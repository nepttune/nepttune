#!/usr/bin/env bash

chmod +x /var/www/html/node_modules/uglify-es/bin/uglifyjs

for FILE in `find /var/www/html/www/js -type f -name '*.js' -not -name '*.min.js'`
do
    /var/www/html/node_modules/.bin/uglifyjs "${FILE}" > "${FILE%.js}.min.js"
done

#!/usr/bin/env bash

for FILE in `find ../www/js -type f -name '*.js' -not -name '*.min.js'`
do
    ../../node_modules/.bin/uglifyjs "${FILE}" > "${FILE%.js}.min.js"
done

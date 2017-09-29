#!/usr/bin/env bash

echo 'Composer'
composer install &> /dev/null

echo 'Bower'
bower install &> /dev/null

echo 'Compiles SCSS'
for FILE in `find ./www/scss -type f -name '*.scss' -not -name '_*'`
do
    sassc "${FILE}" --style compressed > "${FILE%.scss}.css"
done

echo 'Minify JS'
for FILE in `find ./www/js -type f -name '*.js' -not -name '*.min.js'`
do
    uglifyjs "${FILE}" > "${FILE%.js}.min.js"
done

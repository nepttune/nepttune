#!/usr/bin/env bash

if [ ! -e node_modules ]; then
    ln -s www/node_modules node_modules
fi

echo 'Composer'
composer install &> /dev/null

echo 'NPM'
npm install &> /dev/null

echo 'Compile SCSS'
for FILE in `find ./www/scss -type f -name '*.scss' -not -name '_*'`
do
    sassc "${FILE}" --style compressed > "${FILE%.scss}.min.css"
done

echo 'Minify JS'
for FILE in `find ./www/js -type f -name '*.js' -not -name '*.min.js'`
do
    ./node_modules/.bin/uglifyjs "${FILE}" > "${FILE%.js}.min.js"
done

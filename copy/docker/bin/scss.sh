#!/usr/bin/env bash

for FILE in `find /var/www/html/www/scss -type f -name '*.scss' -not -name '_*'`
do
    sassc "${FILE}" --style compressed > "${FILE%.scss}.min.css"
done

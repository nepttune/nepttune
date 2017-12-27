#!/usr/bin/env bash

for FILE in `find ./www/scss -type f -name '*.scss' -not -name '_*'`
do
    sassc "${FILE}" --style compressed > "${FILE%.scss}.min.css"
done

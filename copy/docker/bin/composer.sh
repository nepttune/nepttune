#!/usr/bin/env bash

cd /var/www/html

export COMPOSER_ALLOW_SUPERUSER=1
composer update --no-interaction

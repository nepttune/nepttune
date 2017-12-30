#!/usr/bin/env bash

cd docker
docker-compose exec apache-php /usr/local/bin/composer.sh
docker-compose exec apache-php /usr/local/bin/permission.sh

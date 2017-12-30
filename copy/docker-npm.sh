#!/usr/bin/env bash

cd docker
docker-compose exec apache-php /usr/local/bin/npm.sh
docker-compose exec apache-php /usr/local/bin/permission.sh

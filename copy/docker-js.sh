#!/usr/bin/env bash

cd docker
docker-compose exec apache-php /usr/local/bin/js.sh
docker-compose exec apache-php /usr/local/bin/permission.sh

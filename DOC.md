# Core rules

- Project is run in docker container. Docker and docker-compose are **only** tools required.
- HTTPS is standard. Self signed certificate is created if other isn't provided.
- Files copied into project are gitignored and should not be edited.
- Package already depends on Nette framework and some other libraries, so it's not needed to list them in your `composer.json`.

# Project initialization

First time setup requires manual download of `composer.json` and other necessary docker files. 

1. Create script with following content in your projects directory and run it.
```
#!/usr/bin/env bash

curl -s https://raw.githubusercontent.com/peldax/nette-init/master/default-composer.json > composer.json

mkdir docker
cd docker
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/docker-compose.yml
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/dockerfile-apache-php

mkdir apache2
cd apache2
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/apache2/000-default.conf
cd ..

mkdir bin
cd bin
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/js.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/scss.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/startup.sh
cd ..

mkdir ssl
cd ssl
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/ssl/example
```
This script populates your projects directory with docker files and `composer.json`.

2. Go to docker directory and run docker-compose.

Docker runs `composer`, `npm` and other tools to initiate your project.

# Configuration

- TODO
- Sensitive configuration, such as database connection, should be placed in app/config/local/sensitive.neon. which is gitignored.
(including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.


# Presenters

# Components

## ConfigMenu

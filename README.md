# Nette-init

## Nette Initialization and base library

This package aims to make creating new projects easier. It includes some base library files (presenters and base components), which I use in every project. Then it initializes project with some non-library files and basic directory structure.

## Motivation

Each project shares some base classes, configuration and practices with each other. Those parts are part of our workflow and we change them as our skills evolve. I created this package to automate creation of new projects (copying commonly used files from old projects, creating directory structure, ...), and to avoid unnecessary fuss when some common parts change.

## This package includes 

### Library part

- Base presenters and base components
- Router
- Authenticator
- Custom Form validators
- Some Latte macros and filters
- Layout files (with AdminLTE for non public part and custom componenent for asset loading)

### Copied part

- Directory structure with standard files (bootstrap, index)
- Configuration files with extensions and security headers
- Docker image
- Jenkins configuration

## How to use

- Project is run in docker container. Docker and docker-compose are only tools required.
- HTTPS is standard. Self signed certificate is created if other isn't provided (`docker/ssl/server.crt`, `docker/ssl/server.key`).
- Files copied into project are gitignored and should not be edited (including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.
- Sensitive configuration, such as database connection, should be placed in app/config/local/sensitive.neon. which is gitignored.
- Package already depends on Nette framework and some other libraries, so it's not needed to list them in your composer.json.

## How to setup

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
This script populates your projects directory with docker files and composer.json

3. Go to docker directory and run docker-compose.

Docker runs composer and other tools to initiate your project. Composer handler creates directory structure and copies all library files.


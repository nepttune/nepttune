# Core rules

- Project runs in [docker](#docker) container. `docker` and `docker-compose` are the **only** tools required.
- HTTPS is standard. Self signed certificate is created if other isn't provided.
- Files copied into project are gitignored and should not be edited.
- Package already depends on Nette framework and some other libraries, so it's not needed to list them in `composer.json`.

# Initialization

First time setup requires some extra effort - download of `composer.json` and other necessary [docker](#docker) files. One your project is set up, you dont have to follow this procedure again.

1. Create script with following content in projects directory and run it.
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
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/composer.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/npm.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/scss.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/js.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/permission.sh
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/bin/startup.sh
cd ..

mkdir ssl
cd ssl
curl -sO https://raw.githubusercontent.com/peldax/nette-init/master/copy/docker/ssl/example
```
> This script populates projects directory with docker files and `composer.json`.

2. Go to docker directory and run `docker-compose up`.

> [Docker](#docker) executes `composer`, `npm` and other tools to initiate project automatically.

# Basic usage

## Docker introduction

Project runs in [docker](#docker) container. This approach brings many advantages, here are the most most notable ones.
- The container is identical on all machines. Identical versions of PHP and other libraries make collaboration easier.
- There is no need to install any development tools on your machine.
- There is no need to have multiple services running on your machine (apache, mysql, redis, ...).
There are also few disadvantages of this approach, but this project is designed to minimise their impact.
- Docker is slow on Windows machines. 
  - Whoever is using Windows obviously doesn't care about speed anyway.
- Docker volumes have messy configuration of filesystem permissions.
  - Project includes script to fix filesystem permissions.

### Starting Docker

Docker is started in step 2 of [introduction](#introduction) using `docker-compose up` run in `docker` directory. There is also a script in project root called `docker.sh`, which does it for you.

## Helper scripts

This project also includes several helper scripts to run usefull tools right inside running docker container. Those scripts are located at project root.
- `docker-composer.sh` executes `composer update`.
- `docker-npm.sh` executes `npm update`.
- `docker-scss.sh` minimises `*.scss` files into `*.min.css`.
- `docker-js.sh` minimises `*.js` files using `uglify-es`.
- `docker-permission.sh` fixes filesystem permission for project files. 
  - Sets user ownership to you, so you could access and edit the files.
  - Sets group ownership to apache (`www-data`), so the webserver can read the files.
  - Sets 770 to directories and 660 to regular files.
  - **All scripts above execute this script automaticaly**, there is no need to run it after running `composer` or `uglify-es`.
  - You ming need to call this script after adding a new file.
  
**All helper scripts are automatically executed on container startup.** You dont have to call them manually on every startup.

# Configuration

- TODO
- Sensitive configuration, such as database connection, should be placed in app/config/local/sensitive.neon. which is gitignored.
(including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.

## Extensions

# Router

# Authenticator

# Presenters

# Components

## ConfigMenu

## Breadcrumbs

## Asset loaders (Script and Style)

## Login Form

## User List & Form

# Model

# Form Validator

# Latte extensions

## Macros

## Filters

# Docker

# Jenkins

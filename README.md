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

- Files copied into project are gitignored and should not be edited (including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.
- Sensitive configuration, such as database connection, should be placed in app/config/local/sensitive.neon. which is gitignored.
- Package uses Redis storage by default. Redis server running on your machine is therefore required. Another option is to disable redis storage in configuration.
- Package already depends on Nette framework and other libraries, so it's not needed to list them in your composer.json.

## How to setup

First time setup requires creating `composer.json` and manual download of necessary docker files. 

1. Create file named `composer.json`.
```
{
    "repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/peldax/nette-init.git"
    }
    ],
    "require": {
        "peldax/nette-init": "dev-master"
    },
    "scripts": {
        "post-update-cmd": "Peldax\\NetteInit\\Deploy::init",
        "post-install-cmd": "Peldax\\NetteInit\\Deploy::init"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```
2. Create docker directory and download docker files manually. Or use following helper script, which does it for you.
```
#!/usr/bin/env bash

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
3. Run docker-compose.

Docker runs composer and other tools to initiate your project. Composer handler creates directory structure and copies all library files.


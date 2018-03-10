# Core rules

- Preferable way of running nepttune project is using [docker](#docker) container. `docker` and `docker-compose` are the **only** tools required on your machine.
- HTTPS is standard. Self signed certificate is created if other isn't provided.
- Files which are copied into project directory are gitignored and should not be edited.
- Package already depends on Nette framework and some other libraries, so it's not needed to list them in `composer.json`.

# Initialization

First time setup requires some extra effort - download of `composer.json` and other necessary [docker](#docker) files. Once your project is set up, you dont have to follow this procedure again.

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

2. Go to docker directory and run `docker-compose up`.

[Docker](#docker) executes `composer`, `npm` and other tools to initiate project automatically.

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

Docker is manually started in step 2 of [initialization](#initialization) using `docker-compose up` run in `docker` directory. There is also a script in project root called `docker.sh`, which does it for you.

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

# Layouts

# Components

## BaseComponent

## BaseFormComponent

## BaseListComponent

## Asset loaders (Script and Style)

> Loaded by default.

These components are used to load assets. If used correctly, programmer doesnt have to link any files to layout or anywhere else. In addition, specific files for forms and lists are loaded only if there is a form/list present on current page.

The key is name convention. Assets are loaded using identificators of current view and loaded components.

### View files

Asset loader seeks assets for current module, presenter and action. 

- Module files should be located in `/www/scss/module` and `/www/js/module`. Files should be named same as the module with `.min.js|css` file extension.
- Presenter files should be located in `/www/scss/presenter` and `/www/js/presenter`. Files should be named same as the presenter (including module) with `.min.js|css` file extension.
- Action files should be located in `/www/scss/action` and `/www/js/action`. Files should be named same as the module with `.min.js|css` file extension. Files also need to be divided into folders by presenters name.

### Component files

Asset loader iterates through loaded componenets and seeks it's asset files in `/www/scss/component` and `/www/js/component`. Asset files must have same name as the component with `.min.js|css` file extension. 

Additionally, the loader tries to identify type of component and include asset files for that type.
- If the component name ends with `Form`, loader automatically adds form specific assets (such as `select2`, `icheck`).
- If the component name ends with `List`, loader adds list specific assets as well as form specific assets. Form specific assets are required for inline editing and other list actions.
- If the component name ends with `Stat`, loader automatically adds assets used for statistic components (`chart.js`).

### Example

Current view is `:Admin:User:list`. This page contains component called `UserList`.

Style component first loads (or tries to load) files to page header:

- common files eg. bootstrap
- `/www/scss/module/Admin.min.css` - because of the `Admin` module
- `/www/scss/presenter/Admin:User.min.css` - because of the `User` presenter
- `/www/scss/action/Admin:User/list.min.css` - because of the `list` action

Style component also loads component related files to the bottom of the page.

- common files for list components
- common files for form components
- `/www/scss/component/Userlist.scss` - because of the `UserList` component

Script component loads files to the bottom of the page:

- common files eg. jquery
- `/www/js/module/Admin.min.js` - because of the `Admin` module
- `/www/js/presenter/Admin:User.min.js` - because of the `User` presenter
- `/www/js/action/Admin:User/list.min.js` - because of the `list` action
- `/www/js/component/Userlist.min.js` - because of the `UserList` component

> Loaded the files are not mandatory and can be ommitted if not needed. Most of the time only module files are used, sometimes component requires extra JS.

# Model

# Form Validators

Nepttune includes some extra form validators.

## Same length

This validator ensures that inputs from two controls has the same length - same number of characters. Required parameter is name of second control to test.
```
$form->addText('a', 'A');
$form->addText('b', 'B')
    ->addRule(\Peldax\NetteInit\Validator\CoreValidator::SAME_LENGTH, 'Message', 'a')
```

## Phone number pattern

For validation of phone number inputs there is a constant in `BaseFormComponent` class with responding regex string.
```
$form->addText('phone', 'Phone')
    ->addRule($form::PATTERN, 'Message', BaseFormComponent::PATTERN_PHONE)
```

# Latte extensions

Nepttune includes some common latte macros and filters.

## Macros

### Icon macro

This macro renders as font-awesome icon. First and mandatory icon argument is icon name without the `fa-` prefix. Second optional parameter is icon size, which adds `fa-Sx` class, where `S` is provided parameter value.

```
{icon user, size => 2}
```
Which renders as following HTML:
```
<i class="fa fa-fw fa-user fa-2x"></i>
```

## Filters

No filters at the moment.

# Docker

# Contignous Integration

## Jenkins

## Gitlab CI

# Extensions

Some bigger parts were removed from core package and are available as extensions. The reason behind this is to make Nepttune as lightweight as possible.

## Admin extension

This extension introduces full administration environment including user management and roles.

Documentation for this extension is located in [separate file](https://github.com/nepttune/nepttune/blob/master/DOC-admin.md).

# Extra packages

Some components and other tools are excluded from core package. Can be downloaded as extras or used standalone.

Documentation for extras is located in [separate file](https://github.com/nepttune/nepttune/blob/master/DOC-extra.md).

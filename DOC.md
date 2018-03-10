# Core rules

- Preferable way of running nepttune project is using [docker](#docker) container. `docker` and `docker-compose` are the **only** tools required on your machine.
- HTTPS is standard. Self signed certificate is created if other isn't provided.
- Files which are copied into project directory are gitignored and should not be edited.
- Package already depends on Nette framework and some other libraries, so it's not needed to list them in `composer.json` pr `package.json`.

# Basic usage

This chapter describes Nepttune's dependencies and automation steps in project initialization and startup.

## Example composer.json and package.json

Following snippets contain example `composer.json` and `package.json`. Composer file also includes deploy script which creates directory structure and copies some files into projects directory.
```
{
  "require": {
    "nepttune/nepttune": "^4.0",
    "nepttune/admin": "^4.0",
    "nepttune/extra-navbar": "^4.0"
  },
  "scripts": {
    "post-update-cmd": "Nepttune\\Deploy::init",
    "post-install-cmd": "Nepttune\\Deploy::init"
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```
```
{
  "dependencies": {
    "nepttune": "^1.0.1"
  }
}

```

## Running with Docker

The only requirement for your machine is to have `docker` and `docker-compose` installed. Other tools needed by application are already present in docker container.

### Docker introduction

Nepttune is aimed to run in [docker](#docker) container. This approach brings many advantages, here are the most most notable ones.
- The container is identical on all machines. Identical versions of PHP and other libraries make collaboration easier.
- There is no need to install any development tools on your machine.
- There is no need to have multiple services running on your machine (apache, mysql, redis, ...).

There are also few disadvantages of this approach, but nepttune is designed to minimise their impact.
- Docker is slow on Windows machines. 
  - Whoever is using Windows obviously doesn't care about speed anyway.
- Docker volumes have messy configuration of filesystem permissions.
  - Nepttune includes script to fix filesystem permissions.

### Project initialization

First time setup requires some extra effort - manuall download of necessary [docker](#docker) files. Once the files are there, you dont have to follow this procedure again.

1. Getting the docker files. Files can be manually downloaded from [nepttune/docker](https://github.com/nepttune/docker) package.
2. Starting docker container.
3. Running helper scripts to install dependencies and prepare assets.

Following script does all the steps for you.
```
#!/usr/bin/env bash

mkdir docker
cd docker
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/docker-compose.yml
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/dockerfile-apache-php

mkdir apache2
cd apache2
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/apache2/000-default.conf
cd ..

mkdir bin
cd bin
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/bin/composer.sh
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/bin/npm.sh
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/bin/scss.sh
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/bin/js.sh
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/bin/permission.sh
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/bin/startup.sh
cd ..

mkdir ssl
cd ssl
curl -sO https://raw.githubusercontent.com/nepttune/docker/master/copy/docker/ssl/example

cd ..
docker-compose up -d

#TODO ALL IN ONE SCRIPT
#cd ..
#sh docker-prepare.sh
```

### Project startup

Docker is started using `docker-compose up` run in `docker` directory. There is also a script in project root called `docker.sh`, which does it for you.

### Helper scripts

Nepttune also includes several helper scripts to run usefull tools right inside running docker container. Those scripts are located at projects root.
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
  
**The permission script is automatically executed on container startup.**

## Running without Docker

Project can be run without docker as any other project using Apache (or other webserver), MariaDB and Redis.

# Configuration

Nepttune includes default configuration. **Each setting can be overriden in project specific configuration file.**
Sensitive configuration, such as database login information, should be placed in `app/config/sensitive.neon` which is gitignored.

## Core configuration

This chapter describes common configuration of application.

### Exceptions

By default, application exception catching is enabled. It is useful to override this option in development environment.

### Error Presenter

Nepttune includes its simple implementatiton of Error presenter. If you with to use your own specific presenter to handle errors override the `errorPresenter` configuration.
```
application:
    errorPresenter: SomeOtherPresenter
```

### PHP settings

Nepttune sets php property `date.timezone: Europe/Prague`.

## Default extensions

Nepttune by default enables some Nette extensions which bring extra functionality. Some of them have to be configured and some have optional configuration. Each configuration option can be overriden. For more information explore respective documentation of each extension.

### Redis ([kdyby/redis](https://github.com/Kdyby/Redis))

This extension is required in order to connect to redis database.

Nepttune configures redis server information. If running without docker, you also have to override redis host.
```
redis:
    host: 'X'
```

### Translation ([kdyby/translation](https://github.com/Kdyby/Translation))

This extension brings translator. Each rendered text which comes from Nepttune is translator enabled. Supported languges for now are English and Czech.

Nepttune configures set of supported languages and a default language.

### MobileDetect ([ipub/mobile-detect](https://github.com/iPublikuj/mobile-detect))

This extension is used for client identification.

### Nextras form ([nextras/forms](https://github.com/nextras/forms))

This extenstion helps with form rendering and adds some common form controls.

### Dependent selectbox ([nasext/dependent-select-box](https://github.com/NasExt/DependentSelectBox))

This extenstion adds implementation of dependent selectbox as form control.

### Recaptcha ([uestla/recaptcha-control](https://github.com/uestla/ReCaptchaControl))

This extenstion adds form control which renders Google Recaptcha.

For using this form control it is neccassary to set your recaptcha keys. Those keys should be placed in gitignored `app/config/sensitive.neon`.
```
recaptcha:
    siteKey: 'X'
    secretKey: 'X'
```

## Database

Database configuration sets charset and SQL mode for database connection. It also disables PDO's emulated prepared statements.

It is neccassary to set your project database login information. This information should be placed in gitignored `app/config/sensitive.neon`.

```
database:
    default:
        dsn: 'mysql:host=X;dbname=X'
        user: 'X'
        password: 'X'
```

## Forms

Form configuration sets default error messages.

## Security

Security configuration enhances projects security by setting up session and multiple security headers.

### Session

Nepttune sets session name to the value of `sessionName` parameter and sets its expiration to 14 days.

### Cookies

Nepttune sets cookies to secure and httpOnly (HttpOnly option is already enabled by Nette.), sets cookie path to `/` and cookie domain to value of `domain` parameter (with dot prefix to allow subdomains).

### Security headers

Nepttune sets multiple security headers to recommended values (including `Content Security Policy`). The most important one `Strict Transport Security` is enabled by default, but only with max age 60. It is recommended to override this option and icrease the max age. 

Review values for each header directly in [source code](https://github.com/nepttune/nepttune/blob/master/config/security.neon).

## Parameters

Some of the application specific settings are saved as parameters.

- Meta information for page header
- Special destinations in application (eg. homepage)
- Domain (used for correct configuration of cookies)
- Session name (used for correct cnfiguration of session)

Review and override values for each parameter directly in [source code](https://github.com/nepttune/nepttune/blob/master/config/parameters.neon).

## Services

Nepttune registers router factory, some models and component factories as services. Those services cannot be unset, but cause no harm and do not have to be used.

Named service `router` is set to Nepttune's subdomain router. If you wish to use another custom router, register new router factory and override named service.

# Router

Nepttune includes 2 simple router implementatitons.

## Subdomain

> Used by default.

This router implementation is using following pattern:
```
//<module>.%domain%/[<locale [a-z]{2}>/]<presenter>/<action>[/<id>]
```

Requests subdomain is used as module identification. Www module is therefore frontend of application.

## Standard

This router consists of 2 routes, one to Api module and second to Www module. They are using following patterns:
```
/api/<presenter>/<action>

/[<locale [a-z]{2}>/]<presenter>/<action>[/<id>]
``` 

# Interfaces and Traits

## ITranslatedComponent and TTranslator

Trait `TTranslator` includes translator variable and function to inject Translator. Interface `ITranslatedComponent` gives hint to decorator to call the inject method.

Whenever you need your componenet to have translator, just simply add trait and interface.

# Presenters

## BasePresenter

## ErrorPresenter

# Layouts

# Model

# Components

## BaseComponent

This component assists in rendering and in creation of subcomponents.

## BaseFormComponent

> Uses TTranslator.

## BaseListComponent

> Uses TTranslator.

## AssetLoader

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

Stylesheet files are loaded into page header:

- common files eg. bootstrap
- `/www/scss/module/Admin.min.css` - because of the `Admin` module
- `/www/scss/presenter/Admin:User.min.css` - because of the `User` presenter
- `/www/scss/action/Admin:User/list.min.css` - because of the `list` action

Component related files are linked in the bottom of the page.

- common files for list components
- common files for form components
- `/www/scss/component/Userlist.scss` - because of the `UserList` component

Scripts are inserted to the bottom of the page:

- common files eg. jquery
- `/www/js/module/Admin.min.js` - because of the `Admin` module
- `/www/js/presenter/Admin:User.min.js` - because of the `User` presenter
- `/www/js/action/Admin:User/list.min.js` - because of the `list` action
- `/www/js/component/Userlist.min.js` - because of the `UserList` component

> Loaded the files are not mandatory and can be ommitted if not needed. Most of the time only module files are used, sometimes component requires extra JS.

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

> Loaded by default.

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

# Extensions

Some bigger parts were removed from core package and are available as extensions. The reason behind this is to make Nepttune as lightweight as possible.

## Admin extension

This extension introduces full administration environment including user management and roles.

Documentation for this extension is located in [separate file](https://github.com/nepttune/nepttune/blob/master/DOC-admin.md).

# Extra packages

Some components and other tools are excluded from core package. Can be downloaded as extras or used standalone.

Documentation for extras is located in [separate file](https://github.com/nepttune/nepttune/blob/master/DOC-extra.md).

# Contignous Integration

## Jenkins

## Gitlab CI

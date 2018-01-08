# Core rules

- Project runs in [docker](#docker) container. `docker` and `docker-compose` are the **only** tools required on your machine.
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
> This script populates projects directory with docker files and `composer.json`.

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

# Components

## BaseComponent

## BaseFormComponent

## BaseListComponent

## ConfigMenu

Config Menu is simple component for generating static menu. It is designed to be used as static menu in admininstration layout, but can be used anywhere else. 

Component takes an array as constructor parameter. Array has to have following format.
```
Menu:
    order:
        name: 'Order'
        icon: 'comments'
        dest: 'Order:default'
        role: 'administrator'
Menu2:
    settings:
        name: 'Settings'
        icon: 'cog'
        dest:
            category:
                name: 'Category'
                dest: 'Category:default'
            ingredient:
                name: 'Ingredient'
                dest: 'Ingredient:default'
```
Which renders as following HTML.
```
<ul class="sidebar-menu" data-widget="tree"> 
  <li class="header">Menu</li> 
  <li>
    <a href="/order/">
      <i class="fa fa-comments"></i> 
      <span>Order</span>
    </a> 
  </li> 
  <li class="header">Menu2</li> 
  <li class="treeview">
    <a href="#">
      <i class="fa fa-cog"></i> 
      <span>Settings</span> 
      <span class="pull-right-container"> 
        <i class="fa fa-angle-left pull-right"></i> 
      </span> 
    </a> 
    <ul class="treeview-menu"> 
      <li>
        <a href="/category/">Category</a>
      </li>
      <li>
        <a href="/ingredient/">Ingredient</a>
      </li> 
    </ul> 
  </li> 
</ul>
```
- `Menu` and `Menu2` are non clickable items. Can be used as headers or for multiple menu sections.
- `order` and `settings` are representation of menu items with following options.
  - `dest` - Link destination. Can be array, to create expandable sub-menu.
  - `icon` - Displayed FA icon.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. When `dest` is an array, class is added to every link. (OPTIONAL)
- `category` and `ingredient` are representation of submenu options with following options.
  - `dest` - Link destination.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. (OPTIONAL)

### Recommended usage

```
services:
    menu:
        class: Peldax\NetteInit\Component\ConfigMenu(%configMenu%)
parameters:
    configMenu:
        Menu:
            order:
                name: 'Order'
                icon: 'comments'
                dest: 'Order:default'
                role: 'user'
```

## ConfigNavbar

Config Navbar is another component for generating static menu. It renders as Bootstrap Navbar and therefore is designed to be used as static navigation panel.
Component takes an array as constructor parameter. Array has to have following format.
```
brand:
    name: 'Brand'
    image: '/www/images/brand.png'
    dest: 'Default:default'
settings:
    name: 'Settings'
    dest:
        category:
            name: 'Category'
            dest: 'Category:default'
        ingredient:
            name: 'Ingredient'
            dest: 'Ingredient:default'
```
Which renders as following HTML.
```
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/">
        <img src="/www/images/brand.png" width="30" height="30" alt="Brand">
        Brand
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarDropdown"><span
        class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse collapse" id="navbarDropdown">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown-settings" data-toggle="dropdown">
                    Dropdown link
                </a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="/category/">Category</a>
                  <a class="dropdown-item" href="/ingredient/">Ingredient</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
```
- `settings` is representation of menu item with following options.
  - `dest` - Link destination. Can be array, to create expandable sub-menu.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. When `dest` is an array, class is added to every link. (OPTIONAL)
- `category` and `ingredient` are representation of dropdown options with following options.
  - `dest` - Link destination.
  - `name` - Displayed name.
  - `role` - Role required for user to have in order to display this link. (OPTIONAL)
  - `class` - HTML class added to the link element. (OPTIONAL)
- If `brand` key exists, it is used as brand (header) item with following options.
  - `dest` - Link destination.
  - `name` - Displayed name.
  - `image` - Header image.

### Recommended usage

```
services:
    menu:
        class: Peldax\NetteInit\Component\ConfigNavbar(%configNavbar%)
parameters:
    configNavbar:
        brand:
            name: 'Brand'
            dest: 'Default:default'
            image: '/www/images/brand.png'
        order:
            name: 'Order'
            dest: 'Order:default'
            role: 'user'
```

## Breadcrumbs

> Loaded by default


## Asset loaders (Script and Style)

> Loaded by default

## Login Form

## User List & Form

# Model

# Form Validators

Project includes some extra form validators.

## Same length

This validator ensures that inputs from two controls has the same length - same number of characters. Required parameter is name of second control to test.

```
$form->addText('a', 'A');
$form->addText('b', 'B')
    ->addRule(\Peldax\NetteInit\Validator\CoreValidator::SAME_LENGTH, 'a')
```

# Latte extensions

## Macros

## Filters

# Docker

# Jenkins

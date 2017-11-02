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

- directory structure with standard files (bootstrap, index)
- configuration files with extensions and security headers


## How to use

- Files copied into project are gitignored and should not be edited (including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.
- Sensitive configuration, such as database connection, should be placed in app/config/local/sensitive.neon. which is gitignored.
- Package uses Redis storage by default, so install redis on your machine or disable redis in configuration.
- Package already requires Nette framework and other libraries, so it's no need to list them in your composer.json.
- Here is example composer.json file used to initialize your project.

```
 {
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/peldax/nette-init"
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

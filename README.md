# Nette-init

## Nette Initialization and base library

This package aims to make creating new projects easier. It includes some base library files (presenters and base components), which I use in every project. Then it initializes project with some non-library files and basic directory structure.

## How to use

- Files copied into project are gitignored and should not be edited (including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.
- Project already includes Nette framework and other libraries, so it's no need to list them in your composer.json.
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
    "post-update-cmd": "Peldax\\NetteInit\\Deploy::init()",
    "post-install-cmd": "Peldax\\NetteInit\\Deploy::init()"
  },
  "minimum-stability": "dev",
  "prefer-stable": true
 }
```

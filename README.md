# Nepttune

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

For technical information about this project, visit [documentation](https://github.com/peldax/nette-init/blob/master/DOC.md).

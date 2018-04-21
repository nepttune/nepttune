# Nepttune

:ocean: :trident: Nepttune core package

## Nette Initialization and base library

This package aims to make creating new projects easier. It includes some base library files (presenters and base components), which I use in every project. Then it initializes project with some non-library files and basic directory structure.

## Motivation

Each project shares some base classes, configuration and practices with each other. Those parts are part of our workflow and we change them as our skills evolve. I created this package to automate creation of new projects (copying commonly used files from old projects, creating directory structure, ...), and to avoid unnecessary fuss when some common parts change.

## This package includes 

### Library part

- Base presenters and components
- Premade componenets, including custom asset loader
- Premade layout files
- Configuration files with extensions and security headers
- Multiple router implementaitons
- Authenticator
- Extra Form validators
- Extra Latte macros and filters

### Copied part

- Directory structure with standard files (bootstrap, index)
- Docker image
- CI configuration

## How to use

For technical information about this project, visit [documentation](https://github.com/nepttune/nepttune/blob/master/DOC.md).

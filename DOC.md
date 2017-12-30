# How to use

- Project is run in docker container. Docker and docker-compose are only tools required.
- HTTPS is standard. Self signed certificate is created if other isn't provided (docker/ssl/server.crt, docker/ssl/server.key).
- Files copied into project are gitignored and should not be edited (including configuration files). Configuration can be overriden in local config. Authorizator, router and other services can be overriden in configuration as well.
- Sensitive configuration, such as database connection, should be placed in app/config/local/sensitive.neon. which is gitignored.
- Package already depends on Nette framework and some other libraries, so it's not needed to list them in your composer.json.

#!/usr/bin/env bash

chown -R www-data /var/log/apache2
apache2ctl -D FOREGROUND

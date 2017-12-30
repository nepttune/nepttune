#!/usr/bin/env bash

chown -R :www-data /var/log/apache2
chown -R 1000:www-data /var/www/html
find /var/www/html \( -type f -execdir chmod 660 {} \; \) \
                -o \( -type d -execdir chmod 770 {} \; \)

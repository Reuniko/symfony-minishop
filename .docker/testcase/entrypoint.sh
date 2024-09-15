#!/usr/bin/env bash

composer install --prefer-source;

/usr/sbin/apache2ctl -D FOREGROUND
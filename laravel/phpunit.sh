#!/bin/sh
./artisan cache:clear
vendor/bin/phpunit $@

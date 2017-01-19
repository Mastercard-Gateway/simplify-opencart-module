#!/bin/bash

./run.sh

cd test/phpunit-browserstack

php composer.phar test

docker rm -f mysql
docker rmi -f mysql

docker rm -f opencart
docker rmi -f simplify_open_cart

cd ../..

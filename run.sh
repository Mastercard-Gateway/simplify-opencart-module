#!/bin/bash

docker rm -f mysql
docker rmi -f mysql
docker run -p 8880:3306 --name mysql -e MYSQL_ROOT_PASSWORD=rootpwd -e MYSQL_DATABASE=opencart -d mysql

docker rm -f opencart
docker rmi -f simplify_open_cart
docker build -t simplify_open_cart .
docker run -p 8080:80 --name opencart --link mysql:myql -d simplify_open_cart
docker cp src/upload/. opencart:/var/www/html

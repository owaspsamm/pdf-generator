#!/bin/bash

echo "Running mysql image"
docker run --name samm-mysql -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 --platform linux/x86_64 -d mysql:5.7
while ! wget localhost:3306;
do
  sleep 5
done
echo "mysql image started successfully"


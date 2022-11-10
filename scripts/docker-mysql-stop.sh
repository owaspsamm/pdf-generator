#!/bin/bash

echo "Stopping mysql image"
docker stop samm-mysql
echo "Removing mysql image"
docker rm samm-mysql

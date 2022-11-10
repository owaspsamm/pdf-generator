#!/bin/bash
keep_alive=
while getopts :a arg
do
    case $arg in
    a)  keep_alive=1;;
    ?)  printf "Usage: %s: [-a]\n" "$0"
        exit 1;;
    esac
done

echo "Running mysql setup script"
sh ./scripts/docker-mysql.sh
echo "Finished executing mysql setup script"

echo "Running DB setup script"
sh ./scripts/setup_database.sh
echo "Finished executing DB setup script"

echo "Generating PDF"
php bin/console app:save-pdf

if [ -z "$keep_alive" ]; then
    sh ./scripts/docker-mysql-stop.sh
fi
echo "Finished execution. You can find the generated PDF in the /export folder"




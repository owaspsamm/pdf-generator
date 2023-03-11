#!/bin/bash
keep_alive=0
while getopts ":a:u:b:m:l:" arg;
do
    case "${arg}" in
    a)  PARAM_keep_alive=1;;
    u)  PARAM_URL=${OPTARG};;
    b)  PARAM_BRANCH=${OPTARG};;
    m)  PARAM_MODEL=${OPTARG};;
    l)  PARAM_LANGUAGE=${OPTARG};;
    *)  printf "Usage: %s: [-a, -u 'url', -b 'branch', -m 'model folder', -l 'language']\n" "$0"
        exit 1;;
    esac
done

export PARAM_keep_alive;
export PARAM_URL;
export PARAM_BRANCH;
export PARAM_MODEL;
export PARAM_LANGUAGE;


echo "Running mysql setup script"
sh ./scripts/docker-mysql.sh
echo "Finished executing mysql setup script"

echo "Running DB setup script"
sh ./scripts/setup_database.sh
echo "Finished executing DB setup script"

echo "Generating PDF"
php bin/console app:save-pdf

if [ ! $keep_alive ]; then
    sh ./scripts/docker-mysql-stop.sh
fi


echo "Finished execution. You can find the generated PDF in the /export folder"


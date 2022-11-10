#!/bin/bash

echo "Setting up database"

echo "drop db"
php bin/console doctrine:database:drop --force
echo "create db"
php bin/console doctrine:database:create
echo "migration"
php bin/console doctrine:migrations:migrate --no-interaction

echo "clone / pull OWASP-SAMM model repository"
sh ./scripts/clone_owasp_samm.sh


#!/bin/sh
GITHUB_REPO=https://github.com/owaspsamm/core.git

echo '\nExecuting migrations (if any)...'
php bin/console doctrine:migrations:migrate --no-interaction

if [ ! -d "private/core" ]; then
    mkdir -p private
    cd private
    echo 'Cloning project...'
    git clone $GITHUB_REPO
    cd ..
else
  cd private/core
  echo 'Discarding local changes to OWASP SAMM model (if any)...'
  git checkout -- .
  echo 'Pulling latest changes...'
  git pull
  cd ..
  cd ..
fi

echo 'Syncing...'
php bin/console app:sync-from-owasp-samm

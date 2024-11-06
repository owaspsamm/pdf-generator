#!/bin/sh
GITHUB_REPO=https://github.com/owaspsamm/core.git
BRANCH='main'
MODEL='model'
if [ -n "$PARAM_URL" ]; then
  GITHUB_REPO=$PARAM_URL
fi

if [ -n "$PARAM_BRANCH" ]; then
  BRANCH=$PARAM_BRANCH
fi

if [ -n "$PARAM_MODEL" ]; then
  MODEL=$PARAM_MODEL
fi

if [ -n "$PARAM_LANGUAGE" ]; then
  LANGUAGE=$PARAM_LANGUAGE
fi


basename=$(basename "$GITHUB_REPO")
filename=${basename%.*}

echo '\nExecuting migrations (if any)...'
php bin/console doctrine:migrations:migrate --no-interaction

if [ ! -d "private/$filename" ]; then
    mkdir -p private
    cd private
    echo "Cloning project $GITHUB_REPO"
    git clone "$GITHUB_REPO"
    echo "cd $filename"
    cd $filename
    echo "git checkout $BRANCH"
    git checkout "$BRANCH"
    cd ..
else
  cd "private/$filename"
  echo 'Discarding local changes to OWASP SAMM model (if any)...'
  echo "git checkout $BRANCH"
  git checkout "$BRANCH"
  echo 'Pulling latest changes...'
  git pull
  cd ..
  cd ..
fi

echo 'Current folder...'
pwd

echo 'Syncing...'
php bin/console app:sync-from-owasp-samm "$filename" "$MODEL"
if [ -n "$LANGUAGE" ]; then
  echo "Syncing from $LANGUAGE/$MODEL..."
  php bin/console app:sync-from-owasp-samm "$filename" "$LANGUAGE/$MODEL" 1
fi

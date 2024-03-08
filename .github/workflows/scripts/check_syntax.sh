#!/bin/bash -ex

COMPOSER_CMD="php -d memory_limit=-1 /usr/local/bin/composer"
TOOLING=".github/workflows/scripts/syntax_checker.json"

## For local testing, uncomment these lines, then run the script directly.
#REPO="utnews"
#BRANCH="28-syntax"
#git clone https://github.com/utdk/$REPO.git
#cd $REPO

git checkout -f
git fetch && git checkout develop
git fetch && git checkout $BRANCH

$COMPOSER_CMD validate --no-check-all
# If there are composer validation issues, this will send an exit code of 1.

cp $TOOLING composer.json
$COMPOSER_CMD install --ignore-platform-reqs
PHP_EXTENSIONS="php,inc,module,install,profile,yml"
# Limit to where this branch diverged...
# https://git-scm.com/docs/git-merge-base#_discussion
TO_MERGE=$(git merge-base develop HEAD)
PHP_LIST=$( git diff $TO_MERGE --name-only --diff-filter=ACMRX -- "*.php" "*.inc" "*.yml" "*.module" "*.install" "*.profile")
echo "Changed files:"
echo $PHP_LIST
if [ -z "$PHP_LIST" ]; then
  echo "No matching files in this pull request. Moving on..."
  exit 0
fi
vendor/bin/phpcs --standard="vendor/drupal/coder/coder_sniffer/DrupalPractice/ruleset.xml" $PHP_LIST --extensions=$PHP_EXTENSIONS --exclude=Drupal.InfoFiles.AutoAddedKeys
vendor/bin/phpcs --standard="vendor/drupal/coder/coder_sniffer/Drupal/ruleset.xml" $PHP_LIST --extensions=$PHP_EXTENSIONS --exclude=Drupal.InfoFiles.AutoAddedKeys

# If a PHPCS violation has been found, send exit code to runner.
if [ $? -ne 0 ]; then
  echo "CODE SYNTAX VIOLATION(S) FOUND"
  exit 1
fi

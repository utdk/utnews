#!/bin/bash -ex

set -e

TOOLING=".github/workflows/scripts/"
WORKINGDIR="syntax/"

mkdir -p $WORKINGDIR
cd $WORKINGDIR
# Clean up cloned repository before starting.
if [ -d $REPO ]; then
  rm -rf $REPO
fi

gh repo clone utexas-utdk/$REPO
cd $REPO

# GitHub Actions performs a 'shallow' fetch, so we get more history with --depth
# See https://stackoverflow.com/a/59570673/6046296
git checkout -f
git fetch --depth=100 && git checkout develop
git fetch --depth=100 && git checkout $BRANCH

composer validate --no-check-all
# If there are composer validation issues, this will send an exit code of 1.

# Limit to where this branch diverged...
# https://git-scm.com/docs/git-merge-base#_discussion
TO_MERGE=$(git merge-base develop HEAD)

PHP_EXTENSIONS="php,inc,module,install,profile,theme,yml"
PHP_LIST=$( git diff $TO_MERGE --name-only --diff-filter=ACMRX -- "*.php" "*.inc" "*.yml" "*.module" "*.install" "*.profile" "*.theme")
if [ ! -z "$PHP_LIST" ]; then
  echo "*** Changed PHP files ****"
  echo $PHP_LIST
  cp $TOOLING/syntax_checker.json composer.json
  composer install --ignore-platform-reqs
  EXCLUDE_RULES="Drupal.InfoFiles.AutoAddedKeys,DrupalPractice.Objects.GlobalDrupal,DrupalPractice.FunctionCalls.InsecureUnserialize"
  vendor/bin/phpcs --standard="vendor/drupal/coder/coder_sniffer/DrupalPractice/ruleset.xml" $PHP_LIST --extensions=$PHP_EXTENSIONS --exclude=$EXCLUDE_RULES
  vendor/bin/phpcs --standard="vendor/drupal/coder/coder_sniffer/Drupal/ruleset.xml" $PHP_LIST --extensions=$PHP_EXTENSIONS --exclude=$EXCLUDE_RULES
fi
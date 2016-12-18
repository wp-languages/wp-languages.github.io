#!/bin/bash

# Open the build folder
cd _site

# Create completely new git repo
git init .

# Config git
git config user.name "$GIT_COMMIT_USER"
git config user.email "$GIT_COMMIT_EMAIL"

# Add CNAME for the site
if [ "$SITE_DOMAIN" != "" ]; then
    echo "Adding CNAME: $SITE_DOMAIN"
    echo $SITE_DOMAIN > CNAME
fi

# Add github as remote
git remote add github "https://$GITHUB_ACCESS_TOKEN@github.com/$TRAVIS_REPO_SLUG.git"

# and add all files
git add -A

# Commit all files
git commit -am "Builded gh-pages from $TRAVIS_BRANCH in Travis CI\nCommit-ID:$TRAVIS_COMMIT"

# Push current master branch as gh-pages into github
# Don't output anything so that $GITHUB_ACCESS_TOKEN won't go into the logs
echo "Pushing to https://github.com/$TRAVIS_REPO_SLUG quietly"
git push github $TRAVIS_BRANCH:$TRAVIS_DEPLOY_BRANCH --force --quiet

#!/bin/bash

# Copy templates into build branch
cp _config/travis.yml _site/.travis.yml
cp _config/README.md _site/README.md

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

# sed all outputs so that $GITHUB_ACCESS_TOKEN can be removed from output

# Add github as remote
{ git remote add github "https://$GITHUB_ACCESS_TOKEN@github.com/$TRAVIS_REPO_SLUG.git" 2>&1; } | \
    sed "s|$GITHUB_ACCESS_TOKEN|REDACTED|g"

# and add all files
{ git add -A 2>&1; } | sed "s|$GITHUB_ACCESS_TOKEN|REDACTED|g"

# Commit all files
{ git commit -am "Builded gh-pages from $TRAVIS_BRANCH in Travis CI\nCommit-ID:$TRAVIS_COMMIT" 2>&1; } | \
    sed "s|$GITHUB_ACCESS_TOKEN|REDACTED|g"

# Push current master branch as gh-pages into github
{ git push github master:$TRAVIS_DEPLOY_BRANCH --force 2>&1; } | \
    sed "s|$GITHUB_ACCESS_TOKEN|REDACTED|g"

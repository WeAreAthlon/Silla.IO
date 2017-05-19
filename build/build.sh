#!/bin/sh

# Run from root dir of project.

TOOLS_DIR=vendor/bin

composer install --prefer-dist

# Basic Syntax check
${TOOLS_DIR}/parallel-lint --exclude vendor --exclude temp .

# Code Style Sniffer
${TOOLS_DIR}/phpcs --report=checkstyle --report-file=build/logs/codesniffer-checkstyle.xml --extensions=php --standard=PSR2 --ignore=vendor,public,temp .

# Calculate the size of a PHP project.
${TOOLS_DIR}/phploc phploc --exclude vendor --exclude temp --exclude public --count-tests --log-csv ./build/logs/phploc.csv --log-xml ./build/logs/phploc.xml .

# Code Static Analysis Report
${TOOLS_DIR}/pdepend --jdepend-xml=build/logs/pdepend-jdepend.xml --jdepend-chart=build/logs/pdepend-dependencies.svg --overview-pyramid=build/logs/pdepend-overview-pyramid.svg --ignore=vendor,public,resources,temp .

# Code Mess detector
${TOOLS_DIR}/phpmd . html codesize --reportfile build/logs/pmd.html --exclude vendor,tests,temp,public

# Copy Paste detector
${TOOLS_DIR}/phpcpd --log-pmd build/logs/pmd-cpd.xml --exclude vendor --exclude temp --exclude public .

# Run Automated Tests
${TOOLS_DIR}/phpunit --stderr --configuration build/configurations/phpunit.xml

# Generate Code Documentation Reference
${TOOLS_DIR}/apigen generate --quiet --config build/configurations/apigen.yaml

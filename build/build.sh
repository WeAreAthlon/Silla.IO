#!/bin/sh

# Run from root dir of project.

TOOLS_DIR=vendor/bin

composer install --prefer-dist

echo app cms core tasks tests | xargs -n 1 -P 0 php --syntax-check

${TOOLS_DIR}/phploc --count-tests --log-csv build/logs/phploc.csv --log-xml build/logs/phploc.xml app cms core tests

${TOOLS_DIR}/pdepend --jdepend-xml=build/logs/jdepend.xml --jdepend-chart=build/pdepend/dependencies.svg --overview-pyramid=build/pdepend/overview-pyramid.svg app,cms,core,tasks,tests

${TOOLS_DIR}/phpmd app,cms,core,tasks,tests xml codesize --reportfile build/logs/pmd.xml

${TOOLS_DIR}/phpcs --report=checkstyle --report-file=build/logs/checkstyle.xml --standard=build/configurations/Athlon/ruleset.xml --extensions=php app cms core

${TOOLS_DIR}/phpcpd --log-pmd build/logs/pmd-cpd.xml app cms core tests

${TOOLS_DIR}/phpunit --stderr --configuration build/configurations/phpunit.xml

${TOOLS_DIR}/apigen generate --quiet --config build/configurations/apigen.yaml

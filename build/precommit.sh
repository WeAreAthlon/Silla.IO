#!/bin/sh

# Run from root dir of project. Supply arg path to tests.
# Tool uses unstaged and staged files in git.

TOOLSDIR=vendor/bin
PROMPT="read -p 'Continue...' -n1 -s"
MOD_FILES=$(git diff --name-only HEAD)

echo $MOD_FILES | xargs -n 1 php --syntax-check

eval $PROMPT

$TOOLSDIR/phploc --count-tests $MOD_FILES

eval $PROMPT

$TOOLSDIR/phpcs --report=checkstyle --report-file=build/logs/checkstyle.xml --extensions=php --standard=PSR2 --ignore=vendor,public,temp .

eval $PROMPT

$TOOLSDIR/phpcbf --standard=PSR2  --extensions=php --ignore=tests/,build/,autoload.php $MOD_FILES

eval $PROMPT

$TOOLSDIR/phpcpd $MOD_FILES

eval $PROMPT

for p in $MOD_FILES
do
    if [ -f $p ]
    then
        f=$(basename -s .php $p)
        t=$(find tests/ -type f -name "${f}Test.php")
        if [ -n "$t" ]
        then
            $TOOLSDIR/phpunit --debug --stderr --config build/configurations/phpunit_precommit.xml $t
        fi
    fi
done

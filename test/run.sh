#!/usr/bin/env bash

hash prove 2>&- || { echo >&2 "prove executable not found. please install perl-Test-Harness."; exit 1; }


php_installed=`which php`

hash php 2>&- || { echo >&2 "php executable not found. please install php5.3 or greater."; exit 1; }


version=`/usr/bin/env php -r "echo phpversion();"`
versioncheck=`/usr/bin/env php -r "echo version_compare(phpversion(), '5.3');"`

if [ $versioncheck -lt 1 ]
then
    echo "php5.3+ required. you are running $version. please upgrade."
    exit 1;
fi

echo "riak tests running php $version ..."
echo "-----------------------------------"


f=`dirname $0`

execsupported=`prove -? | grep "\--exec"`

if [ "$execsupported" ]; then
    /usr/bin/env prove -r --exec=php $* "$f/"
else
    /usr/bin/env prove -r $* "$f/"
fi
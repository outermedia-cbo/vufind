#!/bin/bash
#
# sync with libadmin and clear cache

VUFIND_BASE=/usr/local/vufind/vfsblinked
VUFIND_CACHE=$VUFIND_BASE/local/cache

#if [ "$UID"  -ne 0 ]; then
#    echo "You have to be root to use the script because cache will be cleared"
#    exit 1
#fi

BASEDIR=$(dirname $0)
echo $BASEDIR
INDEX="$BASEDIR/../public/index.php"
VUFIND_LOCAL_DIR="$BASEDIR/../local"

export VUFIND_LOCAL_MODULES=Swissbib,Libadmin
export VUFIND_LOCAL_DIR
#export APPLICATION_ENV=development

php $INDEX libadmin sync $@

#please do not delete a directory with options -rf as root based on a relative directory! GH
echo "still to be done by yourself: delete local cache and restart apache"
# no removal of hierarchy cache
#rm -rf $VUFIND_CACHE/searchspecs/*
#rm -rf $VUFIND_CACHE/objects/*
#rm -rf $VUFIND_CACHE/languages/*

#service httpd restart

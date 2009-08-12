#!/bin/sh

SOURCE=$1
DESTINATION=$2
EXCLUDE_FILES="--exclude .svn --exclude .cache --exclude pack_and_publish.bat"

cd $SOURCE && tar -cz $EXCLUDE_FILES -f "$DESTINATION.ezpkg" *
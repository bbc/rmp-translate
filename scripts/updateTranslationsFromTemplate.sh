#!/bin/bash

MSGMERGE=`which msgmerge`

if [ -z "$MSGMERGE" ]; then
    echo "Error: unable to find msgmerge"
    echo "GNU GetText Utilities must be installed and on the path to run this script"
    exit 1
fi

if [ -z "$1" ]; then
    echo "Usage: updateTranslationsFromTemplate.sh programmes"
    exit
fi

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

TRPATH="${SCRIPTPATH}/../src/RMP/Translate/lang/${1}"

for i in `ls "$TRPATH"/*.po`; do
    msgmerge -N --backup=none -U $i "${TRPATH}/${1}.pot"
done

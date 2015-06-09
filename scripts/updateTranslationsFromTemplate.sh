#!/bin/bash

MSGMERGE=`which msgmerge`

if [ -z "$MSGMERGE" ]; then
    echo "Error: unable to find msgmerge"
    echo "GNU GetText Utilities must be installed and on the path to run this script"
    exit 1
fi

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

TRPATH="${SCRIPTPATH}/../src/RMP/Translate/lang/programmes"

for i in `ls "$TRPATH"/*.po`; do
    msgmerge -N --backup=none -U $i "${TRPATH}/programmes.pot"
done
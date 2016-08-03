#!/bin/bash

MSGMERGE=`which msgmerge`

if [ -z "$MSGMERGE" ]; then
    echo "Error: unable to find msgmerge"
    echo "GNU GetText Utilities must be installed and on the path to run this script"
    exit 1
fi

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

DOMAINS=("programmes" "music")

for DOMAIN in "${DOMAINS[@]}"
do

    TRPATH="${SCRIPTPATH}/../src/RMP/Translate/lang/${DOMAIN}"

    for i in `ls "$TRPATH"/*.po`; do
        msgmerge -N --backup=none -U $i "${TRPATH}/${DOMAIN}.pot"
    done

done

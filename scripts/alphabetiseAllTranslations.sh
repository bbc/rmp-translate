#!/bin/bash

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

DOMAINS=("programmes" "music")

for DOMAIN in "${DOMAINS[@]}"
do

    TRPATH="${SCRIPTPATH}/../src/RMP/Translate/lang/${DOMAIN}"

    $SCRIPTPATH/converters/poFileSorter.php "${TRPATH}/${DOMAIN}.pot" "${TRPATH}/${DOMAIN}.pot"
    $SCRIPTPATH/updateTranslationsFromTemplate.sh

done

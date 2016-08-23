#!/bin/bash

if [ -z "$1" ]; then
    echo "Usage: alphabetiseAllTranslations.sh programmes"
    exit
fi

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

TRPATH="${SCRIPTPATH}/../src/RMP/Translate/lang/${1}"

$SCRIPTPATH/converters/poFileSorter.php "${TRPATH}/${1}.pot" "${TRPATH}/${1}.pot"
$SCRIPTPATH/updateTranslationsFromTemplate.sh "${1}"

#!/bin/bash

SCRIPTPATH=$( cd $(dirname $0) ; pwd -P )

TRPATH="${SCRIPTPATH}/../src/RMP/Translate/lang/programmes"

$SCRIPTPATH/converters/poFileSorter.php "${TRPATH}/programmes.pot" "${TRPATH}/programmes.pot"
$SCRIPTPATH/updateTranslationsFromTemplate.sh

#!/usr/bin/env php
<?php
/**
 * Create a new blank translation file from the existing cy.po translation file by blanking all the translations
 */

// MAIN
if (empty($argv[1]) || empty($argv[2])) {
    echo 'createBlankTranslationFile.php $locale $outputFile'."\n";
    echo "Create a new blank translation file from the existing cy.po translation file by blanking all the translations\n";
    exit(1);
}
$inputFileName = dirname(__DIR__) . '/src/RMP/Translate/lang/programmes/cy.po';
$locale = $argv[1];
$outputFileName = $argv[2];

$inputHandle = fopen($inputFileName, 'r');
if ($inputHandle === false) {
    echo "Error could not open file $inputFileName\n";
    exit(1);
}

$outputHandle = fopen($outputFileName, 'w');
if ($outputHandle === false) {
    echo "Error could not open file $outputFileName for writing\n";
    exit(1);
}

while ($line = fgets($inputHandle)) {
    $line = trim($line);
    $newLine = $line;
    if ($line === '"Language: cy_GB\n"') {
        $newLine = '"Language: ' .  $locale . '\n"';
    } elseif (substr($line, 0, 8) === 'msgstr "') {
        $newLine = 'msgstr ""';
    } elseif (substr($line, 0, 7) === 'msgstr[') {
        $numPlural = substr($line, 7, 1);
        $newLine = "msgstr[$numPlural] \"\"";
    }
    fputs($outputHandle, $newLine."\n");
}

print "File $outputFileName created\n";
print "Remember to populate all language_* barlesque system entries before sending it to translators\n";

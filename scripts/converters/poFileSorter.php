#!/usr/bin/env php
<?php
/**
 * Short script to put our .po files in alphabetical order to make them easier to compare/verify
 */

// MAIN
if (empty($argv[1]) || empty($argv[2])) {
    echo 'poFileSorter.php $inputFile $outputFile'."\n";
    exit(1);
}
$inputFileName = $argv[1];
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

$headers = array();
$headerEnded = false;
$messages = array();
$defaultItem = array('key' => null, 'lines' => array());
$item = $defaultItem;
while ($line = fgets($inputHandle)) {
    $line = trim($line);
    if (!$headerEnded) {
        if ($line != '' && substr($line, 0, 1) != '#') {
            $headers[] = $line;
            continue;
        } else {
            $headerEnded = true;
        }
    }
    if ($line === '') {
        // New item indicated by whitespace
        array_push($messages, $item);
        $item = $defaultItem;
    } elseif (substr($line, 0, 7) === 'msgid "') {
        $item['key'] = substr($line, 7, -1);
        $item['lines'][] = $line;
    } else {
        $item['lines'][] = $line;
    }
}
array_push($messages, $item);
fclose($inputHandle);

usort($messages, function ($a, $b) {
    return strcmp($a["key"], $b["key"]);
});

// Print headers
foreach ($headers as $line) {
    fputs($outputHandle, $line."\n");
}
fputs($outputHandle, "\n");

foreach ($messages as $item) {
    if (empty($item['key'])) {
        continue;
    }
    foreach ($item['lines'] as $line) {
        fputs($outputHandle, $line."\n");
    }
    fputs($outputHandle, "\n");
}
fclose($outputHandle);
#!/usr/bin/env php
<?php
/**
 * msgformat2pot.php
 *
 * BIG FAT WARNING: Do not use this script.
 *
 * Horrifically poorly coded CLI PHP script to translate /programmes .res files into .pot
 * template format. This is intended for one off use and you are expected to check the output
 * for sanity. Do no under any circumstances put this into a build script or anything
 * that actually matters.
 *
 * Use: msgformat2pot.php $bundlePath $outputFile.po
 *
 * Puts comments into the PO file indicating the english text for non-english
 * languages.
 * Any untranslated strings are put into the output with msgtext "" so you can add them in a PO editor
 *
 * Delete this once the .res -> .po migration is complete.
 */

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\IcuResFileLoader;
use Symfony\Component\Translation\MessageCatalogue;

// See bottom of the file for the actual script. For some reason the class won't instantiate unless it's first
// Yes, I should split this up into separate files. No, I'm not going to. One of these is bad enough.


require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Handles our MessageFormater strings. Probably will break on many valid MessageFormater strings.
 * it parses all the ones we have though.
 */
class BBCPoFileDumper extends \Symfony\Component\Translation\Dumper\FileDumper
{
    public function format(MessageCatalogue $messages, $domain = 'messages')
    {
        $output = 'msgid ""'."\n";
        $output .= 'msgstr ""'."\n";
        $output .= '"Content-Type: text/plain; charset=UTF-8\n"'."\n";
        //$output .= '"Content-Transfer-Encoding: 8bit\n"'."\n";
        $output .= '"Language: '.$messages->getLocale().'\n"'."\n";
        // 3 Plural form. Some languages can have more than 3 plural forms, this may need tweaking for those
        $output .= '"Plural-Forms: nplurals=3; plural=(n==0 ? 0 : n==1 ? 1 : 2)\n"'."\n";
        // This makes https://localise.biz/free/poeditor accept 3 plural forms. Yay for hacks.
        //$output .= '"X-Loco-Target-Locale: cy_GB\n"'."\n";
        $output .= "\n";

        $newLine = false;
        foreach ($messages->all($domain) as $source => $target) {
            if ($newLine) {
                $output .= "\n";
            } else {
                $newLine = true;
            }
            if (preg_match('/^UNTRANSLATED/', $source)) {
                $source = preg_replace('/^UNTRANSLATED_?/', '', $source);

            }
            $target = $this->fixPlaceHolders($target);
            // Format english language comment where applicable

            // English in comments for translated languages
            $englishTarget = $messages->get($source);
            $englishTarget = $this->fixPlaceHolders($englishTarget);
            if ($englishTarget == $source) {
                $englishTarget = "NO ENGLISH TRANSLATION AVAILABLE YET";
            }
            list($englishIdx, $englishPlurals) = $this->getPluralisedForms($englishTarget);
            if (!empty($englishPlurals)) {
                foreach ($englishPlurals as $idx => $msg) {
                    switch ($idx) {
                        case 0:
                            $output .= '#.No items: "' . $this->escape($msg) . "\". \n";
                            break;
                        case 1:
                            $output .= '#.One item: "' . $this->escape($msg) . "\". \n";
                            break;
                        default:
                            $output .= '#.' . $idx . '+ items: "' . $this->escape($msg) . "\". \n";
                            break;
                    }

                }
            } else {
                $output .= '#.'. $this->escape($englishTarget)."\n";
            }
            if (!empty($englishPlurals)) {
                $output .= sprintf('#:Parameter %d in source'."\n", $englishIdx);
            }

            $output .= sprintf('msgid "%s"'."\n", $this->escape($source));
            if (!empty($englishPlurals)) {
                // Output plural ID
                $output .= sprintf('msgid_plural "%s %%count%%"'."\n", $this->escape($source));
                // Output plural forms
                foreach ($englishPlurals as $index => $msg) {
                    $output .= sprintf('msgstr[%d] ""'."\n", $this->escape($index));
                }
            } else {
                // Standard translation string
                $output .= sprintf('msgstr ""'."\n");
            }
        }

        return $output;
    }

    private function fixPlaceHolders($target)
    {
        // Replace {0} with %1 {1} with %2 etc
        return preg_replace_callback(
            '/\{(\d)\}/',
            function ($matches) {
                $index = $matches[1] + 1;
                return "%$index";
            },
            $target
        );
    }

    private function getPluralisedForms($target)
    {
        if (preg_match('/\{(\d),choice,(.*)\}/', $target, $matches)) {
            // Plural Fun Times
            // This won't generalise well, but it's the only format we actually use, so it should work
            $pluralVarIndex = ($matches[1] + 1);
            $plurals = explode('|', trim($matches[2]));
            foreach ($plurals as $plural) {
                if (substr($plural, 0, 2) == '0#') {
                    $index = 0;
                    $msg = substr($plural, 2);
                } elseif (substr($plural, 0, 2) == '1#') {
                    $index = 1;
                    $msg = substr($plural, 2);
                } elseif (substr($plural, 0, 2) == '1<') {
                    $index = 2;
                    $msg = substr($plural, 2);
                } else {
                    throw new RuntimeException("Invalid or unrecognised messageformat string ($plural) in $target");
                }
                $msg = strtr($msg, array('%'.$pluralVarIndex =>'%count%'));
                $pluralMessages[$index] = $msg;
            }
            return array($pluralVarIndex, $pluralMessages);
        }
        return array(null, null);
    }

    protected function getExtension()
    {
        return 'po';
    }

    private function escape($str)
    {
        return addcslashes($str, "\0..\37\42\134");
    }
}
// MAIN
if (empty($argv[1])) {
    echo 'msgformat2pot.php $bundlePath $outputFilePath'."\n";
    exit(1);
}
define('DEFAULT_LOCALE', 'en_GB');
$locale = DEFAULT_LOCALE;
$bundlePath = $argv[1];
$outPath = $argv[2];
$outputHandle = fopen($outPath, 'w');
if ($outputHandle === false) {
    echo "Error could not open file $outPath for writing\n";
    exit(1);
}

$translate = new Translator($locale, new MessageSelector());

$translate->addLoader('resfile', new IcuResFileLoader());
$translate->addResource('resfile', $bundlePath, $locale);
$defaultCatalogue = null;

$dumper = new BBCPoFileDumper();
fwrite($outputHandle, $dumper->format($translate->getCatalogue($locale), 'messages'));
fclose($outputHandle);
echo "All done here. Output at $outPath\n";

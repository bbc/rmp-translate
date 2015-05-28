#!/usr/bin/env php
<?php
/**
 * msgformat2po.php
 *
 * BIG FAT WARNING: Do not use this script.
 *
 * Horrifically poorly coded CLI PHP script to translate /programmes .res files into .po
 * format. This is intended for one off use and you are expected to check the output
 * for sanity. Do no under any circumstances put this into a build script or anything
 * that actually matters.
 *
 * Use: msgformat2po.php (en_GB|cy_GB|gd_GB|ga_IE) $bundlePath $outputFile.po
 *
 * Puts the correct locale and pluralisation type into the .po file.
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
 * Handles our MessageFormater strings. Yay. Mostly stolen from Symfony. Also Yay. Probably will break on
 * many valid MessageFormater strings. Less yay, but fuck it, it parses all the ones we have.
 */
class BBCPoFileDumper extends \Symfony\Component\Translation\Dumper\FileDumper
{
    /**
     * {@inheritdoc}
     */
    public function format(MessageCatalogue $messages, $domain = 'messages', MessageCatalogue $defaultMessages = null, $createBlank = false)
    {
        $output = 'msgid ""'."\n";
        $output .= 'msgstr ""'."\n";
        $output .= '"Content-Type: text/plain; charset=UTF-8\n"'."\n";
        $output .= '"Content-Transfer-Encoding: 8bit\n"'."\n";
        $output .= '"Language: '.$messages->getLocale().'\n"'."\n";
        // 3 Plural form. Some languages can have more than 3 plural forms, this may need tweaking for those
        $output .= '"Plural-Forms: nplurals=3; plural=(n==0 ? 0 : n==1 ? 1 : 2)\n"'."\n";
        // This makes https://localise.biz/free/poeditor accept 3 plural forms. Yay for hacks.
        $output .= '"X-Loco-Target-Locale: cy_GB\n"'."\n";
        $output .= "\n";

        $newLine = false;
        foreach ($messages->all($domain) as $source => $target) {
            if ($newLine) {
                $output .= "\n";
            } else {
                $newLine = true;
            }
            $blankTranslations = false;
            if (preg_match('/^UNTRANSLATED/', $source)) {
                $source = preg_replace('/^UNTRANSLATED_?/', '', $source);
                $blankTranslations = true;
            } elseif ($createBlank) {
                $blankTranslations = true;
            }
            $target = $this->fixPlaceHolders($target);
            // Format english language comment where applicable
            if ($defaultMessages) {
                // English in comments for translated languages
                $englishTarget = $defaultMessages->get($source);
                $englishTarget = $this->fixPlaceHolders($englishTarget);
                list($englishIdx, $englishPlurals) = $this->getPluralisedForms($englishTarget);
                if (!empty($englishPlurals)) {
                    foreach ($englishPlurals as $idx => $msg) {
                        $output .= '#.'.$idx.' : '. $this->escape($msg)."\n";
                    }
                } else {
                    $output .= '#.'. $this->escape($englishTarget)."\n";
                }
            }
            $output .= sprintf('msgid "%s"'."\n", $this->escape($source));
            list($pluralVarIndex, $pluralMessages) = $this->getPluralisedForms($target);
            if (!empty($pluralMessages)) {
                // Output plural ID
                $output .= sprintf('msgid_plural "%s %%count%%"'."\n", $this->escape($source));
                $output .= sprintf('#:Parameter %d in source'."\n", $pluralVarIndex);
                // Output plural forms
                foreach ($pluralMessages as $index => $msg) {
                    if ($blankTranslations) {
                        $msg = '';
                    }
                    $output .= sprintf('msgstr[%d] "%s"'."\n", $this->escape($index), $this->escape($msg));
                }
            } else {
                // Standard translation string
                if ($blankTranslations) {
                    $target = '';
                }
                $output .= sprintf('msgstr "%s"'."\n", $this->escape($target));
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

    /**
     * {@inheritdoc}
     */
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
if (empty($argv[1]) || empty($argv[2]) || empty($argv[3])) {
    echo 'msgformat2po.php $locale $bundlePath $outputFilePath $createBlankTrFile'."\n";
    exit(1);
}
define('DEFAULT_LOCALE', 'en_GB');
$locale = $argv[1];
$bundlePath = $argv[2];
$outPath = $argv[3];
$createBlankTrFile = (!empty($argv[4]) && $argv[4] && $argv[4] != 'false') ? true : false;
$outputHandle = fopen($outPath, 'w');
if ($outputHandle === false) {
    echo "Error could not open file $outPath for writing\n";
    exit(1);
}

$translate = new Translator($locale, new MessageSelector());

$translate->addLoader('resfile', new IcuResFileLoader());
$translate->addResource('resfile', $bundlePath, $locale);
$defaultCatalogue = null;
if ($locale != DEFAULT_LOCALE) {
    $translate->addResource('resfile', $bundlePath, DEFAULT_LOCALE);
    $defaultCatalogue = $translate->getCatalogue(DEFAULT_LOCALE);
}
$dumper = new BBCPoFileDumper();
fwrite($outputHandle, $dumper->format($translate->getCatalogue($locale), 'messages', $defaultCatalogue, $createBlankTrFile));
fclose($outputHandle);
echo "All done here. Output at $outPath\n";

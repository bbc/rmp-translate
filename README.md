RMP\Translate
=============
A small composer library to load .po (GetText) translation files and provide a simple API for translations in /programmes and Amen.

This is esentially a wrapper that simplifies the use of Symfony\Translation.  

## Why?

/programmes is currently being translated into a wide variety of languages for BBC World Serivce. This requires a translation file format that non-technical users can safely handle. GetText is about the only widely supported format with fully featured editors (including a [web based one](https://localise.biz/free/poeditor/demo)).

GetText is based around feeding strings in a source language to a translation layer, and having those translated into a target language. BBC localisation generally uses placeholder strings which are replaced with a localised string at run time. 

RMP\Translate simplifes the usage of this translation format with Symfony translate, provides a script to convert existing msgformat (BBC_Localisation) files to .po files, and works around some of the bugs that crop up using Symfony translate. 


## Basic Usage

### Installation

First you'll need to include the library in your composer.json like so:


     "repositories": [
            {
                "type": "vcs",
                "url": "git@github.com:bbc/rmp-translate.git"
            }
        ],
        "require": {
            "bbc-rmp/translate": "dev-master"
        }


### Instantiation

You then need to set up the configuration and instantiate the translate class. A basic example follows. More details
on the configuration options and recommended usage are included below(link?)
```php
use RMP\Translate\TranslateFactory;
use RMP\Translate\Translate;
// Set to any supported language
$locale = 'en_GB';
$options = array();
$factory = new TranslateFactory();
$translate = $factory->create($locale, $options);
```
### Translation

Gettext input files and PHP code for some basic examples.

Simple translation:

```
msgid "all_episodes_iplayer"
msgstr "All episodes available on BBC iPlayer"
```
```php
$placeholder = 'all_episodes_iplayer';
$englishText = $translate->translate($placeholder);
// $englishText now contains  "All episodes available on BBC iPlayer"
```
With substition:
```
msgid "category_title"
msgstr "Programmes categorised as %1"
```
```php
$placeholder = 'category_title';
$englishText = $translate->translate($placeholder, array('%1' => 'Factual');
// $englishText now contains  "Programmes categorised as Factual"
```
With pluralisation:
```
msgid "available_count"
msgid_plural "available_count %count%"
msgstr[0] "There are currently no available episodes"
msgstr[1] "There is currently %count% available episode"
msgstr[2] "There are currently %count% available episodes"
```
```php
$placeholder = 'available_count';
$noItemsText = $translate->translate($placeholder, array('%count%' => 0), 0);
// $noItemsText = 'There are currently no available episodes'
$someItemsText = $translate->translate($placeholder, array('%count%' => 3), 3);
// $someItemsText = 'There are currently 3 available episodes'
```
## Technical Detail

### Features
We support
* Fallback to a standard language (e.g. English) when translation string is not found in the current locale.
* Multiple message domains for translation, all loaded at once or separately (e.g. separate translation sets for radio and programmes if necessary)
* Loading direct from PO files (a pure text format, no complex build scripts). A caching facility is provided as instantiation of a full translation set from a .po file is expensive (~35ms)
* Logging via the observer pattern

### Configuration options
RMP\Translate provides extensive configuration options, which are supplied to TranslateFactory as an associative array. E.g.
```php
$factory = new TranslateFactory();
$options = array('fallback_locale' => 'en_GB', 'cachepath' => '/var/cache/bbc-pal-programmes/translate');
$translate = $factory->create('cy_GB', $options);
```
Supported options:
* **fallback_locale**: A language to pull translations from when a translation is not found in the default language. In the above example, an English translation will be used if a Welsh translation is not found. Defaults to disabled. 
* **cachepath**: A directory, writable by your apache user, used by Symfony to store compiled versions of your .po files. Defaults to null (no caching). It is strongly recommended this be set in production. *WARNING*: Once Symfony writes a complied translation file it will not overwrite it even if Apache is restarted. You therefore need to version your cache directories in production or clear them on deployment. 
* **domains**: Accepts an array of message domains (e.g. programmes, radio, iplayer), each containing a different translation set. Defaults to 'programmes'. 
* **default_domain**: The message domain to pull translations from when another domain is not explicitly specified in the call to translate(). Defaults to 'programmes'
* **debug**: Enables cache invalidation in Symfony translate. Basically, this means that if you change your .po file, the cached version will be automatically updated. Do not set this in production for performance reasons. Defaults to false. 
* **basepath**: Allows you to specify a base path for your .po files anywhere in the filesystem, or relative to the include path. Read TranslateFactory for more info. But it's probably best not to touch this and leave all translation files in their default localtion at [src/RMP/Translate/lang/](https://github.com/bbc/rmp-translate/tree/master/src/RMP/Translate/lang).

### Instantiation
It's best to only instantiate RMP\Translate once and store the resulting object somewhere for reuse (we use a singleton in /progs). Instantiation is relatively expensive, while translation is extremely cheap. 

Note that TranslateFactory->construct() will throw a TranslationFilesNotFoundException if none of your supplied domains/languages/fallback languages actually exists. It will not complain as long as something  valid is actually found, so make sure your language files are actually being loaded during testing. 

### Logging
You can get logging data from RMP\Translate by attaching an Observer. E.g. 
```php
$factory = new TranslateFactory();
$translate = $factory->create($locale, $options);
$logger = new Logger(); // Logger can be anything that Implements our TranslateObserverInterface
$translate->attach($logger);
```
The logger will recieve [TranslateEvent](https://github.com/bbc/rmp-translate/blob/master/src/RMP/Translate/TranslateEvent.php) objects which should be trivial to log using your existing logging infrastructure. 

Currently the logger will be notified in the event of missing translations or fallback translations. 

### Unit Tests
There is a full suite of unit tests. Simply run PHPUnit in the project root. 
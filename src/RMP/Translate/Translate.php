<?php

namespace RMP\Translate;

use RMP\Translate\TranslateEvent;
use RMP\Translate\TranslateObserverInterface;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\PluralizationRules;
use Symfony\Component\Translation\Translator;

class Translate
{
    /**
     * @var TranslateObserverInterface[]
     */
    protected $observers = array();

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $options;

    /**
     * Default setup for this class
     *
     * @var array
     */
    protected $defaultOptions = array(
        // Base path for .po files
        'translatePath' => null,
        // Writable cache directory for Symfony. Please read the readme before setting
        'cacheDir' => null,
        // Use (e.g.) english translations when translation not found
        'fallbackLocale' => '',
        // Set to true to enable cache invalidation by Symfony
        'debug' => false,
        // List all domains you want to load
        'domains' => array('messages')
    );

    /**
     * @param string $locale
     * @param array $options See $this->defaultOptions
     * @throws Exception
     */
    public function __construct($locale, array $options = array())
    {
        $this->locale = $locale;
        $this->options = array_merge($this->defaultOptions, $options);

        // Default path for language files is ./lang/$domain/$locale.po
        if (!$this->options['translatePath']) {
            $this->options['translatePath'] = __DIR__ . DIRECTORY_SEPARATOR . 'lang';
        }

        $this->translator = new Translator(
            $this->locale,
            new MessageSelector(),
            $this->options['cacheDir'],
            $this->options['debug']
        );

        $this->translator->addLoader('pofile', new PoFileLoader());

        // Allow multiple domains to be defined (e.g. 'radio' and 'programmes')
        foreach ($this->options['domains'] as $domain) {
            $this->translator->addResource('pofile', $this->getFilePath($this->locale, $domain), $this->locale);
        }

        /**
         * Fix the mess that Symfony makes of our pluralisation rules. We may need to change this for some languages
         * in the future that have > 3 plural forms. I look forward to that day with great anticipation and joy.
         */
        PluralizationRules::set(array($this, 'pluralisationRule'), $this->locale);

        /**
         * Support fallback to english if translation not found
         * Note we're not actually using the built in fallback as this breaks because of Symfony's
         * incorrect parsing of .po files. Just once I want to use a module without glaringly obvious bugs
         */
        if ($options['fallbackLocale'] && $options['fallbackLocale'] != $this->locale) {
            foreach ($this->options['domains'] as $domain) {
                $path = $this->getFilePath($options['fallbackLocale'], $domain);
                $this->translator->addResource('pofile', $path, $options['fallbackLocale']);
            }
            PluralizationRules::set(array($this, 'pluralisationRule'), $options['fallbackLocale']);
        }
    }

    /**
     * Translate something. Duh.
     *
     * @param string $key
     *     The translation id/key
     * @param array $substitutions
     *     Associative array of substitutions for placeholders in the translated string
     * @param int|null $pluralisation
     *     The number of "things" for pluralisation purposes. NULL indicates not to pluralise, zero is a valid number
     * @param string $domain
     *     Domain to search for the translation in, defaults to "messages"
     * @return string
     */
    public function translate($key, $substitutions = array(), $pluralisation = null, $domain = 'messages')
    {
        $result = $this->_translate($key, $substitutions, $pluralisation, $domain, $this->locale);

        if (!$result && $this->options['fallbackLocale']) {
            $result = $this->_translate($key, $substitutions, $pluralisation, $domain, $this->options['fallbackLocale']);
            if ($result) {
                $logData = array('key' => $key, 'domain' => $domain, 'locale' => $this->locale);
                $this->notify(new TranslateEvent(TranslateEvent::FALLBACK, $logData));
            }
        }
        if (!$result) {
            $logData = array('key' => $key, 'domain' => $domain, 'locale' => $this->locale);
            $this->notify(new TranslateEvent(TranslateEvent::MISSING, $logData));
            $result = $key;
        }
        return $result;
    }

    /**
     * @param string $key
     * @param array $substitutions
     * @param int|null $pluralisation
     * @param string $domain
     * @param string $locale
     * @return string
     */
    protected function _translate($key, $substitutions, $pluralisation, $domain, $locale)
    {
        if (is_null($pluralisation)) {
            // Singular form
            $result = $this->translator->trans($key, $substitutions, $domain, $locale);
        } else {
            // Plural form
            $key = "$key %1";
            $result = $this->translator->transChoice($key, $pluralisation, $substitutions, $domain, $locale);
        }
        if (empty($result) || $result == $key) {
            return '';
        }
        return $result;
    }

    /**
     * Observer pattern to allow logging of failed translations etc.
     *
     * @param TranslateObserverInterface $observer
     */
    public function attach(TranslateObserverInterface $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * @param TranslateEvent $translateEvent
     */
    public function notify(TranslateEvent $translateEvent)
    {
        foreach ($this->observers as $observer) {
            $observer->translateEventHandler($translateEvent);
        }
    }

    /**
     * This is a callback for Symfony\Component\Translation\PluralizationRules::get
     * Input is the number of items for pluralisation from the call to $this->translate
     * Return value must be the number in our .po file (msgstr[$number] "...")
     *
     * @param int $number
     * @return int
     */
    public function pluralisationRule($number)
    {
        if ($number === 0) {
            return 0;
        }
        if ($number == 1) {
            return 1;
        }
        return 2;
    }

    /**
     * @param $locale
     * @param string $domain
     * @return string
     * @throws Exception
     */
    protected function getFilePath($locale, $domain = 'messages')
    {
        if (!$locale) {
            $locale = $this->locale;
        }
        // Prevent anything nasty in the path
        $locale = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $locale);
        $domain = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $domain);
        $path = $this->options['translatePath'] . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR . $locale . '.po';
        if (file_exists($path)) {
            return $path;
        }
        // Search include paths
        $paths = explode(':', get_include_path());
        foreach ($paths as $path) {
            $temp = $path . DIRECTORY_SEPARATOR . $this->options['translatePath'] . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR . $locale . '.po';
            if (file_exists($temp)) {
                return $temp;
            }
        }
        $this->notify(TranslateEvent::FILENOTFOUND, array('locale' => $locale, 'domain' => $domain))
        throw new Exception("Localisation file not found: " . $path);
    }
}

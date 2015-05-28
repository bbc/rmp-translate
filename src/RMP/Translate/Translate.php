<?php

namespace RMP\Translate;

use RMP\Translate\TranslateEvent;
use RMP\Translate\TranslateObserverInterface;
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
     * Default domain
     *
     * @var string
     */
    protected $domain;

    /**
     * Main locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Fallback Locale
     *
     * @var string
     */
    protected $fallbackLocale;

    /**
     * @param Translator $translator
     * @param string $domain
     * @param string $locale
     * @param string|null $fallbackLocale
     */
    public function __construct(Translator $translator, $domain, $locale, $fallbackLocale = null)
    {
        $this->translator = $translator;
        $this->domain = $domain;
        $this->locale = $locale;
        $this->fallbackLocale = $fallbackLocale;

        /**
         * Fix the mess that Symfony makes of our pluralisation rules. We may need to change this for some languages
         * in the future that have > 3 plural forms. I look forward to that day with great anticipation and joy.
         */
        PluralizationRules::set(array($this, 'pluralisationRule'), $this->locale);

        if ($fallbackLocale && $fallbackLocale != $locale) {
            PluralizationRules::set(array($this, 'pluralisationRule'), $fallbackLocale);
        }
    }

    /**
     * Translate something.
     *
     * @param string $key
     *     The translation id/key
     * @param array $substitutions
     *     Associative array of substitutions for placeholders in the translated string
     * @param int|null $pluralisation
     *     The number of "things" for pluralisation purposes. NULL indicates not to pluralise, zero is a valid number
     * @param string $domain
     *     Domain to search for the translation in, default set in constructor
     * @return string
     */
    public function translate($key, $substitutions = array(), $pluralisation = null, $domain = null)
    {
        if (!$domain) {
            $domain = $this->domain;
        }
        $result = $this->_translate($key, $substitutions, $pluralisation, $domain, $this->locale);

        /**
         * We explicitly check the default and fallback locales rather than rely on Symfony's fallback
         * locale because it's PO file parsing is broken and therefore fallback locales
         * don't work properly. See https://github.com/symfony/symfony/issues/13483
         */
        if ($result === '' && $this->fallbackLocale) {
            $result = $this->_translate($key, $substitutions, $pluralisation, $domain, $this->fallbackLocale);
            if ($result) {
                $logData = array('key' => $key, 'domain' => $domain, 'locale' => $this->locale);
                $this->notify(new TranslateEvent(TranslateEvent::FALLBACK, $logData));
            }
        }
        if ($result === '') {
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
            $key = "$key %count%";
            $result = $this->translator->transChoice($key, $pluralisation, $substitutions, $domain, $locale);
        }
        // We don't want to return the key when the translation is not found (which is GetText standard behaviour)
        if (!$this->translator->getCatalogue($locale)->defines($key, $domain)) {
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
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
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
}

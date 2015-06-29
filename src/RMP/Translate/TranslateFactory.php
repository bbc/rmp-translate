<?php

namespace RMP\Translate;

use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * Class TranslateFactory
 *
 * Create instances of RMP\Translate
 *
 * @package RMP\Translate
 */
class TranslateFactory
{
    /**
     * @param string $locale
     * @param array $options
     * @return Translate
     * @throws TranslationFilesNotFoundException
     */
    public function create($locale, array $options = array())
    {
        /**
         * Default setup for this class
         */
        $defaultOptions = array(
            // Base path for .po files. Can be specified relative to include path (e.g. BBC/Programmes/bundles)
            'basepath' => null,
            // Writable cache directory for Symfony. Please read the readme before setting
            'cachepath' => null,
            // Use (e.g.) english translations when translation not found
            'fallback_locale' => '',
            // Set to true to enable cache invalidation by Symfony
            'debug' => false,
            // List all domains (translation sets) you want to load
            'default_domain' => 'programmes',
            // List all domains (translation sets) you want to load
            'domains' => array('programmes')
        );
        $options = array_merge($defaultOptions, $options);

        $locale = $this->fixLocale($locale);
        $options['fallback_locale'] = $this->fixLocale($options['fallback_locale']);

        if (!in_array($options['default_domain'], $options['domains'])) {
            $options['default_domain'] = reset($options['domains']);
        }

        if (empty($options['cachepath'])) {
            $options['cachepath'] = null;
        }

        $translator = $this->getTranslator(
            $locale,
            null,
            $options['cachepath'],
            $options['debug']
        );

        // Default path for language files is ./lang/$domain/$locale.po
        if (!$options['basepath']) {
            $options['basepath'] = __DIR__ . DIRECTORY_SEPARATOR . 'lang';
        }

        $filesFound = false;

        $translator->addLoader('pofile', new PoFileLoader());

        // Allow multiple domains to be defined (e.g. 'radio' and 'programmes')
        foreach ($options['domains'] as $domain) {
            $path = $this->getFilePath($options['basepath'], $locale, $domain);
            if ($path) {
                $filesFound = true;
                $translator->addResource(
                    'pofile',
                    $path,
                    $locale,
                    $domain
                );
            }
        }

        /**
         * We explicitly check the default and fallback locales rather than rely on Symfony's fallback
         * locale because it's PO file parsing is broken and therefore fallback locales
         * don't work properly. See https://github.com/symfony/symfony/issues/13483
         */
        if ($options['fallback_locale'] && $options['fallback_locale'] != $locale) {
            foreach ($options['domains'] as $domain) {
                $path = $this->getFilePath($options['basepath'], $options['fallback_locale'], $domain);
                if ($path) {
                    $filesFound = true;
                    $translator->addResource('pofile', $path, $options['fallback_locale'], $domain);
                }
            }
        }

        if (!$filesFound) {
            throw new TranslationFilesNotFoundException('No translation files found with basepath ' . $options['basepath']);
        }

        return new Translate($translator, $options['default_domain'], $locale, $options['fallback_locale']);
    }

    /**
     * This is just a wrapper function to enable easier testing
     *
     * @param string $locale
     * @param MessageSelector|null $messageSelector
     * @param string $cachepath
     * @param bool $debug
     * @return Translator
     */
    protected function getTranslator($locale, $messageSelector, $cachepath, $debug)
    {
        return new Translator(
            $locale,
            $messageSelector,
            $cachepath,
            $debug
        );
    }

    /**
     * @param string $basePath
     * @param string $locale
     * @param string $domain
     * @return string
     * @throws Exception
     */
    protected function getFilePath($basePath, $locale, $domain)
    {
        // Strip the _GB etc. bit off the locale for portability
        if (strlen($locale) > 3) {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
        }
        // Prevent anything nasty in the path
        $locale = preg_replace('/[^A-Za-z0-9_\-]/', '', $locale);
        $domain = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $domain);
        $temp = $basePath . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR . $locale . '.po';
        if (file_exists($temp)) {
            return $temp;
        }
        // Search include paths
        $paths = explode(':', get_include_path());
        foreach ($paths as $path) {
            $temp = $path . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR . $locale . '.po';
            if (file_exists($temp)) {
                return $temp;
            }
        }
        return false;
    }

    /**
     * Replace dashes with underscores in locale. Symfony chokes on dashes.
     *
     * @param string $locale
     * @return string
     */
    protected function fixLocale($locale)
    {
        return str_replace('-', '_', $locale);
    }
}

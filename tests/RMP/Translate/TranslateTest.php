<?php

namespace Test\RMP\Translate;

use PHPUnit_Framework_TestCase;
use RMP\Translate\Translate;

/**
 * Class TranslateTest
 *
 * Test the Translate class in isolation
 *
 * @package Test\RMP\Translate
 */
class TranslateTest extends PHPUnit_Framework_TestCase
{
    public $translator;

    public $catalogue;

    public function setUp()
    {
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->catalogue = $this->getMockBuilder('Symfony\Component\Translation\MessageCatalogue')
            ->disableOriginalConstructor()
            ->getMock();
        $this->translator->expects($this->any())
            ->method('getCatalogue')
            ->will($this->returnValue($this->catalogue));
    }
    //$key, $substitutions, $domain, $locale
    public function testBasicTranslation()
    {
        $domain = 'messages';
        $locale = 'en';
        $secondLocale = 'zz';
        $this->translator->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                array('key', array(), $domain, $locale),
                array('key', array(), $domain, $secondLocale)
            )
            ->will($this->onConsecutiveCalls('en_result', 'zz_result'));

        $this->catalogue->expects($this->exactly(2))
            ->method('defines')
            ->with('key')
            ->will($this->returnValue(true));

        $translate = new Translate($this->translator, $domain, $locale);
        $this->assertEquals('en_result', $translate->translate('key'));

        // Test setting the Locale changes it
        $translate->setLocale('zz');
        $this->assertEquals('zz_result', $translate->translate('key'));
    }

    public function testPluralTranslation()
    {
        $domain = 'messages';
        $locale = 'en';
        $this->translator->expects($this->once())
            ->method('transChoice')
            ->with('key %count%', 3, array('%count%'=>3), $domain, $locale)
            ->will($this->returnValue('teststring'));

        $this->catalogue->expects($this->once())
            ->method('defines')
            ->with('key %count%')
            ->will($this->returnValue(true));

        $translate = new Translate($this->translator, $domain, $locale);
        $value = $translate->translate('key', array('%count%'=>3), 3);
        $this->assertEquals('teststring', $value);
    }

    public function testFallbackTranslationEmptyString()
    {
        $domain = 'adomain';
        $locale = 'zz';
        $fallback = 'ru';
        $this->translator->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                array('keyname', array('%1'=>'subst'), $domain, $locale),
                array('keyname', array('%1'=>'subst'), $domain, $fallback)
            )->will($this->onConsecutiveCalls('', 'fallbackstring'));

        $this->catalogue->expects($this->exactly(2))
            ->method('defines')
            ->with('keyname')
            ->will($this->returnValue(true));

        $translate = new Translate($this->translator, $domain, $locale, $fallback);
        $value = $translate->translate('keyname', array('%1'=>'subst'));
        $this->assertEquals('fallbackstring', $value);
    }

    public function testFallbackTranslationUndefined()
    {
        $domain = 'adomain';
        $locale = 'zz';
        $fallback = 'ru';
        $this->translator->expects($this->exactly(2))
            ->method('trans')
            ->withConsecutive(
                array('keyname', array('%1'=>'subst'), $domain, $locale),
                array('keyname', array('%1'=>'subst'), $domain, $fallback)
            )->will($this->onConsecutiveCalls('keyname', 'fallbackstring'));

        $this->catalogue->expects($this->exactly(2))
            ->method('defines')
            ->with('keyname')
            ->will($this->onConsecutiveCalls(false, true));

        $translate = new Translate($this->translator, $domain, $locale, $fallback);
        $value = $translate->translate('keyname', array('%1'=>'subst'));
        $this->assertEquals('fallbackstring', $value);
    }
}

<?php

namespace Test\RMP\Translate;

use RMP\Translate\TranslateFactory;

/**
 * Class BasicDataTest
 *
 * Test some basic things are present in all translation files.
 * This is essentially an error check to ensure translators don't remove or change
 * system information.
 *
 * Add new languages here when ready.
 *
 * @package Test\RMP\Translate
 */
class BasicDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testEnglishProvider
     */
    public function testEnglish($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('en-GB', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testEnglishProvider()
    {
        return array(
            array('language_code', "en-GB"),
            array('language_name', "English (UK)"),
            array('language_direction', "ltr"),
            array('language_barlesque', "en-GB"),
            array('language_barlesque_ws', "false"),
            array('language_locale', "en_GB"),
        );
    }

    /**
     * @dataProvider testWelshProvider
     */
    public function testWelsh($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('cy-GB', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testWelshProvider()
    {
        return array(
            array('language_code', "cy"),
            array('language_name', "Welsh"),
            array('language_direction', "ltr"),
            array('language_barlesque', "cy-GB"),
            array('language_barlesque_ws', "false"),
            array('language_locale', "cy_GB"),
        );
    }

    /**
     * @dataProvider testGaelicProvider
     */
    public function testGaelic($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('ga-IE', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testGaelicProvider()
    {
        return array(
            array('language_code', "ga"),
            array('language_name', "Irish Gaelic"),
            array('language_direction', "ltr"),
            array('language_barlesque', "ga"),
            array('language_barlesque_ws', "false"),
            array('language_locale', "ga"),
        );
    }

    /**
     * @dataProvider testScotsGaelicProvider
     */
    public function testScotsGaelic($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('gd-GB', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testScotsGaelicProvider()
    {
        return array(
            array('language_code', "gd"),
            array('language_name', "Gaelic (Scottish)"),
            array('language_direction', "ltr"),
            array('language_barlesque', "gd"),
            array('language_barlesque_ws', "false"),
            array('language_locale', "gd_GB"),
        );
    }
}

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

    /**
     * @dataProvider testFrenchAfriqueProvider
     */
    public function testFrenchAfrique($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('fr-002', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testFrenchAfriqueProvider()
    {
        return array(
            array('language_code', "fr"),
            array('language_name', "French (Afrique)"),
            array('language_direction', "ltr"),
            array('language_barlesque', "fr-002"),
            array('language_barlesque_ws', "true"),
            array('language_locale', "fr_FR"),
        );
    }

    /**
     * @dataProvider testHausaProvider
     */
    public function testHausa($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('ha-GH', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testHausaProvider()
    {
        return array(
            array('language_code', "ha"),
            array('language_name', "Hausa"),
            array('language_direction', "ltr"),
            array('language_barlesque', "ha-GH"),
            array('language_barlesque_ws', "true"),
            array('language_locale', "ha_GH"),
        );
    }

    /**
     * @dataProvider testKinyarwandaProvider
     */
    public function testKinyarwanda($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('rw-RW', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testKinyarwandaProvider()
    {
        return array(
            array('language_code', "rw"),
            array('language_name', "Kinyarwanda"),
            array('language_direction', "ltr"),
            array('language_barlesque', "rw-RW"),
            array('language_barlesque_ws', "true"),
            array('language_locale', "rw_RW"),
        );
    }

    /**
     * @dataProvider testIndonesianProvider
     */
    public function testIndonesian($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('id-ID', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testIndonesianProvider()
    {
        return array(
            array('language_code', "id"),
            array('language_name', "Indonesian"),
            array('language_direction', "ltr"),
            array('language_barlesque', "id-ID"),
            array('language_barlesque_ws', "true"),
            array('language_locale', "id_ID"),
        );
    }

    /**
     * @dataProvider testSomaliProvider
     */
    public function testSomali($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('so-SO', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testSomaliProvider()
    {
        return array(
            array('language_code', "so"),
            array('language_name', "Somali"),
            array('language_direction', "ltr"),
            array('language_barlesque', "so-SO"),
            array('language_barlesque_ws', "true"),
            array('language_locale', "so_SO"),
        );
    }

    /**
     * @dataProvider testSomaliProvider
     */
    public function testSwahili($key, $value)
    {
        $factory = new TranslateFactory();
        $translate = $factory->create('so-SO', array('domains' => array('programmes')));
        $this->assertEquals($value, $translate->translate($key));
    }

    public function testSwahiliProvider()
    {
        return array(
            array('language_code', "sw"),
            array('language_name', "Swahili"),
            array('language_direction', "ltr"),
            array('language_barlesque', "sw-KE"),
            array('language_barlesque_ws', "true"),
            array('language_locale', "sw_KE"),
        );
    }
}

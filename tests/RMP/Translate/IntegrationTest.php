<?php

namespace Test\RMP\Translate;

use RMP\Translate\TranslateFactory;

/**
 * Class IntegrationTest
 *
 * Testing these classes together is likely to prove more useful than in isolation.
 * This tests the language files in ./lang
 *
 * @package RMP\Translate
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslateFactory $factory
     */
    public $factory;

    public function setUp()
    {
        $this->factory = new TranslateFactory();
    }

    public function testSimpleTranslation()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
        );
        $translate = $this->factory->create('en_GB', $options);
        $value = $translate->translate('test_key_1');
        $this->assertEquals('Test Value 1', $value);
    }

    /**
     * @dataProvider simplePluralProvider
     */
    public function testSimplePlurals($key, $num, $subst, $value)
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
        );
        $translate = $this->factory->create('en_GB', $options);
        $translation = $translate->translate($key, $subst, $num);
        $this->assertEquals($value, $translation);
    }

    public function simplePluralProvider()
    {
        return array(
            array('test_plural_1', 0, array('%count%'=>'anything'), 'Current sanity level: None'),
            array('test_plural_1', 1, array('%count%'=>'anything'), 'Current sanity level One Percent. Good work.'),
            array('test_plural_1', 2, array('%count%'=>'2'), 'Current sanity level 2%'),
            array('test_plural_1', 50, array('%count%'=>'50'), 'Current sanity level 50%'),
        );
    }

    public function testSimpleSubstitution()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
        );
        $translate = $this->factory->create('en_GB', $options);
        $translation = $translate->translate('test_placeholder_1', array('%1'=>'a very small shell script'));
        $this->assertEquals('You have been replaced with a very small shell script', $translation);
    }

    /**
     * @dataProvider simpleFallbackProvider
     */
    public function testFallback($key, $num, $subst, $value)
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'fallback_locale' => 'en_GB',
        );
        $translate = $this->factory->create('ru_RU', $options);
        $translation = $translate->translate($key, $subst, $num);
        $this->assertEquals($value, $translation);
    }

    public function simpleFallbackProvider()
    {
        return array(
            array('test_key_1', null, array(), 'Test Value 1(russian)'),
            array('test_plural_1', 0, array('%count%'=>'anything'), 'Current sanity level: None (russian)'),
            array('test_plural_1', 22, array('%count%'=>'22'), 'Current sanity level 22%(russian)'),
            array('test_englishonly_1', null, array('%count%'=>'50'), 'This should always be in english'),
        );
    }

    /**
     * @dataProvider simpleDomainsProvider
     */
    public function testDomains($key, $num, $subst, $domain, $value)
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'fallback_locale' => 'en_GB',
            'domains' => array('messages', 'otherdomain'),
            'default_domain' => 'otherdomain'
        );
        $translate = $this->factory->create('ru_RU', $options);
        $translation = $translate->translate($key, $subst, $num, $domain);
        $this->assertEquals($value, $translation);
    }

    public function simpleDomainsProvider()
    {
        return array(
            array('test_key_1', null, array(), null, 'Test Value 1(otherdomain,russian)'),
            array('test_key_1', null, array(), 'messages', 'Test Value 1(russian)'),
            array('test_plural_1', 0, array('%count%'=>'anything'), null, 'Current sanity level: None (otherdomain,russian)'),
            array('test_plural_1', 0, array('%count%'=>'anything'), 'messages', 'Current sanity level: None (russian)'),
            array('test_plural_1', 1, array('%count%'=>'1'), null, 'Current sanity level One Percent. Good work.(otherdomain,russian)'),
            array('test_plural_1', 1, array('%count%'=>'1'), 'messages', 'Current sanity level One Percent. Good work.(russian)'),
            array('test_otherdomain_only_1', null, array(), null, 'Some text(otherdomain, russian)'),
            array('test_otherdomain_only_1', null, array(), 'messages', 'test_otherdomain_only_1'),
            array('test_englishonly_1', null, array(), null, 'This should always be in english(otherdomain, english)'),
            array('test_englishonly_1', null, array(), 'messages', 'This should always be in english'),
        );
    }
}

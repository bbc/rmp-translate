<?php

namespace Test\RMP\Translate;

use PHPUnit_Framework_TestCase;
use RMP\Translate\TranslateFactory;

/**
 * Class TranslateFactoryTest
 *
 * This tests the TranslateFactory. I haven't jumped through 400 hoops to inject every bloody
 * dependency. These tests are somewhat coupled to the implementation, but this simple job is complex enough already.
 *
 * @package RMP\Translate
 */
class TranslateFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TranslateFactory $translateFactory
     */
    public $translateFactory;

    public $translator;

    public function setUp()
    {
        $this->translateFactory = $this->getMock('RMP\Translate\TranslateFactory', array('getTranslator'));
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->translateFactory->expects($this->once())
            ->method('getTranslator')
            ->will($this->returnValue($this->translator));
    }

    public function testCreateTranslateBasic()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'domains' => array('messages'),
        );
        $poFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . 'en.po';
        $this->translator->expects($this->once())
            ->method('addResource')
            ->with($this->isType('string'), $this->equalTo($poFilePath), $this->stringContains('en'));

        $translate = $this->translateFactory->create('en-GB', $options);
        $this->assertInstanceOf('RMP\Translate\Translate', $translate);
    }

    public function testCreateTranslateWithFallback()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'domains' => array('messages'),
            'fallback_locale' => 'en-GB'
        );
        $poFilePath1 = __DIR__ . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . 'ru.po';
        $poFilePath2 = __DIR__ . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . 'en.po';
        $this->translator->expects($this->exactly(2))
            ->method('addResource')
            ->withConsecutive(
                array($this->isType('string'), $this->equalTo($poFilePath1), $this->stringContains('ru')),
                array($this->isType('string'), $this->equalTo($poFilePath2), $this->stringContains('en'))
            );

        $translate = $this->translateFactory->create('ru-RU', $options);
        $this->assertInstanceOf('RMP\Translate\Translate', $translate);
    }

    public function testCreateTranslateWithDomain()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'default_domain' => 'otherdomain',
            'domains' => array('otherdomain'),
            'fallback_locale' => 'en-GB'
        );

        $poFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'otherdomain' . DIRECTORY_SEPARATOR . 'en.po';
        $this->translator->expects($this->exactly(1))
            ->method('addResource')
            ->with($this->isType('string'), $this->equalTo($poFilePath), $this->stringContains('en'));

        $translate = $this->translateFactory->create('en_GB', $options);
        $this->assertInstanceOf('RMP\Translate\Translate', $translate);
    }

    public function testCreateTranslateWithDomains()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'domains' => array('messages', 'otherdomain')
        );
        $poFilePath1 = __DIR__ . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . 'ru.po';
        $poFilePath2 = __DIR__ . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'otherdomain' . DIRECTORY_SEPARATOR . 'ru.po';

        $this->translator->expects($this->exactly(2))
            ->method('addResource')
            ->withConsecutive(
                array($this->isType('string'), $this->equalTo($poFilePath1), $this->stringContains('ru')),
                array($this->isType('string'), $this->equalTo($poFilePath2), $this->stringContains('ru'))
            );

        $translate = $this->translateFactory->create('ru-RU', $options);
        $this->assertInstanceOf('RMP\Translate\Translate', $translate);
    }

    public function testParametersPassed()
    {
        $options = array(
            'basepath' => __DIR__ . DIRECTORY_SEPARATOR . 'lang',
            'domains' => array('messages', 'otherdomain'),
            'cachepath' => 'somepath',
            'debug' => true
        );
        $locale = 'en_GB';
        $this->translateFactory->expects($this->once())
            ->method('getTranslator')
            ->will($this->returnValue($this->translator))
            ->with($this->stringContains('en'), null, $options['cachepath'], $options['debug']);

        $translate = $this->translateFactory->create($locale, $options);
        $this->assertInstanceOf('RMP\Translate\Translate', $translate);
    }
}

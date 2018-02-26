<?php

namespace Test\RMP\Translate;

use PHPUnit_Framework_TestCase;
use ReflectionMethod;
use RMP\Translate\TranslateFactory;

/**
 * Class LocaleTruncationTest
 *
 * This tests the truncateLocaleString method in the TranslateFactory.
 *
 * @package RMP\Translate
 */
class LocaleTruncationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider localeProvider
     */
    public function testTruncateLocaleString($locale, $expected)
    {
        $method = new ReflectionMethod('RMP\Translate\TranslateFactory', 'truncateLocaleString');
        $method->setAccessible(true);

        $translateFactory = new TranslateFactory();
        $result = $method->invoke($translateFactory, $locale);

        $this->assertEquals($expected, $result);
    }

    public function localeProvider()
    {
        return [
            'general' => ['en_GB', 'en'],
            'multiple' => ['en_GB_GB', 'en'],
            'short' => ['en', 'en'],
            'noSuffix' => ['articles', 'articles'],
        ];
    }
}

<?php

namespace Test\RMP\Translate;

use RMP\Translate\DateCorrection;

class DateCorrectionTest extends \PHPUnit_Framework_TestCase
{
    private $helper;

    public function setUp()
    {
        $this->helper = new DateCorrection();
    }

    public function testUntouched()
    {
        $this->assertEquals(
            'This is something or nothing',
            $this->helper->fixSpelling('This is something or nothing','en')
        );
    }

    /**
     * @dataProvider testExampleProvider
     * @param $input
     * @param $expected
     */
    public function testFixedExamples($language, $input, $expected)
    {
        $this->assertEquals($expected, $this->helper->fixSpelling($input, $language));
    }

    public function testExampleProvider()
    {
        return array(

            // latin script
            array('cy', 'ABCDEFGGorffenafEFGH', 'ABCDEFGGorffennafEFGH'),
            array('cy', 'B Gorffenaf A Gorffenaf C', 'B Gorffennaf A Gorffennaf C'),
            array('cy-GB', 'B Gorffenaf A Gorffenaf C', 'B Gorffennaf A Gorffennaf C'),
            array('cy_GB', 'B Gorffenaf A Gorffenaf C', 'B Gorffennaf A Gorffennaf C'),

            // UTF8 Script
            array('hi', 'सितम्बर', 'सितंबर'),
            array('hi', 'RRRRसितम्बरRRRR', 'RRRRसितंबरRRRR'),
            array('hi', 'म्सितम्बरम्', 'म्सितंबरम्'),
            array('si', 'පෙබරවාර', 'පෙබරවාරි'),
            array('ur', 'مار چ', 'مارچ'),

            // non PHP supported language, change from English
            array('rw', 'Monday', 'Kuwa mbere'),
            array('rw', 'Mon', 'mbe.'),

        );
    }
}
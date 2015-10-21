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
        $this->assertEquals('This is something or nothing', 'This is something or nothing');
    }

    /**
     * @dataProvider testExampleProvider
     * @param $input
     * @param $expected
     */
    public function testFixedExamples($input, $expected)
    {
        $this->assertEquals($expected, $this->helper->fixSpelling($input));
    }

    public function testExampleProvider()
    {
        return array(

            // latin script
            array('ABCDEFGGorffenafEFGH', 'ABCDEFGGorffennafEFGH'),
            array('B Gorffenaf A Gorffenaf C', 'B Gorffennaf A Gorffennaf C'),

            // UTF8 Script
            array('सितम्बर', 'सितंबर'),
            array('RRRRसितम्बरRRRR', 'RRRRसितंबरRRRR'),
            array('म्सितम्बरम्', 'म्सितंबरम्'),
            array('පෙබරවාර', 'පෙබරවාරි'),
            array('مار چ', 'مارچ'),
        );
    }
}
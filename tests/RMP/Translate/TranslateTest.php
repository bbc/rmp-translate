<?php

namespace RMP\Translate;

use PHPUnit_Framework_TestCase;

class TranslateTest extends PHPUnit_Framework_TestCase
{
    public function testShit()
    {
        $i = new Translate();
        $r = $i->sayShit();
        $this->assertEquals('shit', $r);
    }
}

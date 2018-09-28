<?php

namespace Tests;

use HelloBase\Supports\Integer;

class IntegerTest extends TestCase
{
    public function testBinToInt()
    {
        $this->assertEquals(5, Integer::binToInt(Integer::intToBin(5)));
    }

    public function testIntToBin()
    {
        $this->assertEquals(5, Integer::binToInt(Integer::intToBin(5)));
    }
}

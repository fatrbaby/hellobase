<?php

namespace HelloBase\Supports;

class Integer
{
    public static function longToInt(string $bin)
    {
        return unpack('N2', $bin);
    }
}

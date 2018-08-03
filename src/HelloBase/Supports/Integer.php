<?php

namespace HelloBase\Supports;

class Integer
{
    public static function int64ToInt32(string $bin)
    {
        return unpack('N2', $bin);
    }
}

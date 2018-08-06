<?php

namespace HelloBase\Supports;

class Integer
{
    public static function binToInt(string $bin): int
    {
        list($higher, $lower) = array_values(unpack('N2', $bin));
        $unPackedValue = $higher << 32 | $lower;

        return $unPackedValue;
    }
}

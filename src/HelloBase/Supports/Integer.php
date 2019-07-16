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

    /**
     * this method is from PHP Document
     * @see http://php.net/manual/en/function.pack.php
     * @param $var
     * @return int|mixed
     */
    public static function intToBin($var)
    {
        $f = is_int($var) ? "pack" : "unpack";
        $var = $f("J", $var);

        return is_array($var) ? $var[1] : $var;
    }
}

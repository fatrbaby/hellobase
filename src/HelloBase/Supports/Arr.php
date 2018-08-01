<?php

namespace HelloBase\Supports;

class Arr
{
    public static function get(array $array, $key, $default = null)
    {
        return $array[$key] ?? $default;
    }
}

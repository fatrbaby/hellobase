<?php

namespace HelloBase\Contracts;

interface Table
{
    public function put(string $key, $value): bool ;

    public function row(string $key, array $columns = [], $timestamp = null): array;

    public function rows(string $key, array $columns = [], $timestamp = null): array;

    public function scan($start = null, $stop = null, $prefix = null, $columns = null);
}

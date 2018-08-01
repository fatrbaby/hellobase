<?php

namespace HelloBase\Contracts;

interface Table
{
    public function put(string $row, array $value): bool ;

    public function row(string $row, array $columns = [], $timestamp = null): array;

    public function rows(array $rows, array $columns = [], $timestamp = null): array;

    public function scan($start = null, $stop = null, $prefix = null, $columns = null);
}

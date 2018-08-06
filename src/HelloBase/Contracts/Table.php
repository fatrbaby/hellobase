<?php

namespace HelloBase\Contracts;

interface Table
{
    public function put(string $row, array $value): bool;

    public function row(string $row, array $columns = [], $timestamp = null): array;

    public function rows(array $rows, array $columns = [], $timestamp = null): array;

    public function scan(string $start = '', string $stop = '', array $columns = [], array $with = []);

    public function increment(string $row, string $column, int $amount = 1): bool;
}

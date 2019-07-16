<?php

namespace HelloBase\Contracts;

interface Connection
{
    public function connect();

    public function close();

    public function table($name): Table;

    public function tables(): array;

    public function createTable($table, array $columnFamilies): bool;
}

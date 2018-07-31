<?php

namespace HelloBase;

use HelloBase\Contracts\Table as TableContract;

class Table implements TableContract
{
    protected $table;
    protected $connection;

    public function __construct(string $table, Connection $connection)
    {
        $this->table = $table;
        $this->connection = $connection;
    }

    /**
     * @param string $key
     * @param $values
     * @return bool
     * @throws \Exception
     */
    public function put(string $key, $values): bool
    {
        $command = new Command($this);

        try {
            $command->put($key, $values);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function row(string $key, array $columns = [], $timestamp = null): array
    {
    }

    public function rows(string $key, array $columns = [], $timestamp = null): array
    {
    }

    public function scan($start = null, $stop = null, $prefix = null, $columns = null)
    {
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}

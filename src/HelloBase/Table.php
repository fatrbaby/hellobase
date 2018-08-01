<?php

namespace HelloBase;

use Hbase\TRowResult;
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
     * @param array $values
     * @return bool
     * @throws \Exception
     */
    public function put(string $key, array $values): bool
    {
        $command = new Command($this);

        try {
            $command->put($key, $values);
            return $command->execute() > 0;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function row(string $row, array $columns = [], $timestamp = null): array
    {
        $client = $this->connection->getClient();

        if (is_null($timestamp)) {
            $data = $client->getRowWithColumns($this->table, $row, $columns, []);
        } else {
            $data = $client->getRowWithColumnsTs($this->table, $row, $columns, $timestamp, []);
        }

        return count($data) ? $this->formatRow($data[0]) : [];
    }

    public function rows(array $rows, array $columns = [], $timestamp = null): array
    {
        $client = $this->connection->getClient();

        if (!is_null($timestamp)) {
            $data = $client->getRowsWithColumnsTs($this->table, $rows, $columns, $timestamp, []);
        } else {
            $data = $client->getRowsWithColumns($this->table, $rows, $columns, []);
        }

        return $this->formatRows($data);
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

    protected function formatRows(array $result)
    {
        $formatted = array();

        foreach ($result as $row) {
            $formatted[$row->row] = $this->formatRow($row);
        }

        return $formatted;
    }

    protected function formatRow(TRowResult $result)
    {
        $formatted = [];

        foreach ($result->columns as $column => $value) {
            $formatted[$column] = $value->value;
        }

        return $formatted;
    }
}

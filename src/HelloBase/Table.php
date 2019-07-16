<?php

namespace HelloBase;

use Exception;
use Hbase\IOError;
use Hbase\TIncrement;
use Hbase\TRowResult;
use HelloBase\Contracts\Table as TableContract;

;

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
     * @throws Exception
     */
    public function put(string $key, array $values): bool
    {
        try {
            $putter = new Putter($this);
            $putter->pick($key, $values);
            return $putter->send() > 0;
        } catch (Exception $exception) {
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

    /**
     * @param string $start
     * @param string $stop
     * @param array $columns
     * @param array $with
     * @return \Generator
     * @throws IOError
     * @throws \Hbase\IllegalArgument
     */
    public function scan(string $start = '', string $stop = '', array $columns = [], array $with = [])
    {
        $client = $this->connection->getClient();

        $scannerId = $client->scannerOpenWithStop(
            $this->table,
            $start,
            $stop,
            $columns,
            $with
        );

        try {
            while ($list = $client->scannerGetList($scannerId, 50)) {
                foreach ($list as $result) {
                    yield $result->row => $this->formatRow($result);
                }
            }

            $client->scannerClose($scannerId);
        } catch (Exception $exception) {
            $client->scannerClose($scannerId);

            throw $exception;
        }
    }

    /**
     * @param string $row
     * @param string $column
     * @param int $amount
     * @return bool
     * @throws \Hbase\IOError
     */
    public function increment(string $row, string $column, int $amount = 1): bool
    {
        $increment = new TIncrement([
            'table' => $this->table,
            'row' => $row,
            'column' => $column,
            'ammount' => $amount,
        ]);

        try {
            $this->connection->getClient()->increment($increment);
        } catch (IOError $error) {
            throw $error;
        }

        return true;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    protected function formatRows(array $rows)
    {
        $formatted = [];

        foreach ($rows as $row) {
            $formatted[$row->row] = $this->formatRow($row);
        }

        return $formatted;
    }

    protected function formatRow(TRowResult $row)
    {
        $formatted = [];

        foreach ($row->columns as $column => $value) {
            $formatted[$column] = $value->value;
        }

        return $formatted;
    }
}

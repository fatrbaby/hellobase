<?php

namespace HelloBase;

use Exception;
use Hbase\BatchMutation;
use Hbase\HbaseClient;
use Hbase\Mutation;

class Putter
{
    protected $table;
    protected $mutations = [];

    /**
     * Putter constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function pick($row, array $data)
    {
        if (!isset($this->mutations[$row])) {
            $this->mutations[$row] = [];
        }

        foreach ($data as $column => $value) {
            $this->mutations[$row][] = new Mutation([
                'column' => $column,
                'value' => $value,
                'isDelete' => false
            ]);
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    public function send()
    {
        $commands = [];

        foreach ($this->mutations as $row => $mutation) {
            $commands[] = new BatchMutation(['row' => $row, 'mutations' => $mutation]);
        }

        if (empty($commands)) {
            return 0;
        }

        /**
         * @var $client HbaseClient
         */
        $client = $this->table->getConnection()->getClient();

        try {
            $client->mutateRows($this->table->getTable(), $commands, []);
        } catch (Exception $exception) {
            throw $exception;
        }

        $this->reset();

        return count($commands);
    }

    public function reset()
    {
        $this->mutations = [];
    }
}

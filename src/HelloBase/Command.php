<?php

namespace HelloBase;

use Hbase\BatchMutation;
use Hbase\HbaseClient;
use Hbase\Mutation;

class Command
{
    protected $size = 0;
    protected $table;
    protected $mutations = [];
    protected $mutationCount = 0;

    /**
     * Command constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function put($row, $data)
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

        $this->mutationCount += count($data);

        if ($this->size && $this->mutationCount > $this->size) {
            return $this->execute();
        }

        return 0;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function execute()
    {
        $bms = [];

        foreach ($this->mutations as $row => $mutation) {
            $bms[] = new BatchMutation(['row' => $row, 'mutations' => $mutation]);
        }

        if (empty($bms)) {
            return 0;
        }

        /**
         * @var $client HbaseClient
         */
        $client = $this->table->getConnection()->getClient();

        try {
            $client->mutateRows($this->table->getTable(), $bms, []);
        } catch (\Exception $exception) {
            throw $exception;
        }

        return count($bms);
    }
}

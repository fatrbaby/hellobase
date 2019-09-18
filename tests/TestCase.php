<?php


namespace Tests;


use HelloBase\Connection;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    public function setUp()
    {
        parent::setUp();

        $this->connection = new Connection();
        $this->connection->connect();

        $tables = $this->connection->tables();

        if (!in_array('hellobase', $tables)) {
            $this->connection->createTable('hellobase', ['hb:']);
        }
    }

    public function tearDown()
    {
        $this->connection->close();
        parent::tearDown();
    }
}

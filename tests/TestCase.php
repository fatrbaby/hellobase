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
        $this->connection = new Connection([]);
        $this->connection->connect();
    }

    public function tearDown()
    {
        $this->connection->close();
        parent::tearDown();
    }
}

<?php

namespace Tests;

class ConnectionTest extends TestCase
{
    public function testTables()
    {
        $this->connection->connect();
        $tables = $this->connection->tables();

        $this->assertCount(4, $tables);
    }
}

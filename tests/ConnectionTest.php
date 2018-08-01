<?php

namespace Tests;

class ConnectionTest extends TestCase
{
    public function testTables()
    {
        $tables = $this->connection->tables();

        $this->assertCount(4, $tables);
    }
}

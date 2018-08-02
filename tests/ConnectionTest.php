<?php

namespace Tests;

class ConnectionTest extends TestCase
{
    public function testCreateTable()
    {
        $this->assertTrue(true);
    }

    public function testTables()
    {
        $tables = $this->connection->tables();

        $this->assertArrayHasKey('hellobase', array_flip($tables));
    }
}

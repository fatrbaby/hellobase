<?php

namespace Tests;

class ConnectionTest extends TestCase
{
    public function testTables()
    {
        $tables = $this->connection->tables();

        $this->assertCount(4, $tables);
    }

    public function testCreateTable()
    {
        $created = $this->connection->createTable('hellobase', ['hb:']);

        $this->assertTrue($created);

        $tables = $this->connection->tables();

        $this->assertArrayHasKey('hellobase', array_flip($tables));
    }
}

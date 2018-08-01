<?php

namespace Tests;

class ConnectionTest extends TestCase
{
    public function testTables()
    {
        $tables = $this->connection->tables();

        $this->assertCount(4, $tables);
    }

    public function testPut()
    {
        $table = $this->connection->table('mytable');

        $result = $table->put('fourth', ['cf:name' => 'fatrbaby', 'cf:gender' => 'male']);

        $this->assertNotEmpty($result);
    }
}

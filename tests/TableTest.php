<?php

namespace Tests;

class TableTest extends TestCase
{
    public function testPut()
    {
        $table = $this->connection->table('mytable');

        $result = $table->put('fourth', ['cf:name' => 'fatrbaby', 'cf:gender' => 'male']);

        $this->assertNotEmpty($result);
    }

    public function testRow()
    {
        $table = $this->connection->table('mytable');
        $row = $table->row('fourth', ['cf:name']);

        $this->assertArrayHasKey('cf:name', $row);
    }

    public function testRows()
    {
        $table = $this->connection->table('mytable');

        $rows = $table->rows(['first', 'third', 'fourth']);
        
        $this->assertCount(3, $rows);
    }
}

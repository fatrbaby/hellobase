<?php

namespace Tests;

class TableTest extends TestCase
{
    public function testPut()
    {
        $table = $this->connection->table('hellobase');

        $result = $table->put('first', ['hb:name' => 'fatrbaby', 'hb:gender' => 'male']);

        $this->assertNotEmpty($result);
    }

    public function testRow()
    {
        $table = $this->connection->table('hellobase');
        $row = $table->row('first', ['hb:name']);

        $this->assertArrayHasKey('hb:name', $row);
    }

    public function testRows()
    {
        $table = $this->connection->table('hellobase');

        $rows = $table->rows(['first']);

        $this->assertCount(1, $rows);
    }

    public function testScan()
    {
        $table = $this->connection->table('hellobase');

        foreach ($table->scan() as $row) {
            $this->assertNotEmpty($row);
        }
    }

    public function testIncrement()
    {
        $table = $this->connection->table('hellobase');
        $value = $table->increment('second', 'hb:count', 2);
        $this->assertTrue($value);
    }
}

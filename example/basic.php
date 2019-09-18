<?php

require __DIR__ . '/../vendor/autoload.php';

use HelloBase\Connection;
use HelloBase\Supports\Integer;
use Hbase\IOError;

$connection = new Connection();
$connection->connect();

try {
    print_r($connection->tables());
} catch (IOError $e) {
    exit($e->getMessage());
}

$table = $connection->table('hellobase');

$table->put('111', ['hb:id' => 'id:111', 'hb:name' => 'name:111']);

foreach ($table->scan() as $row => $columns) {
    var_dump($row, $columns);
}

$table->increment('222', 'hb:counter', 5);

$raw = $table->row('222');

if (isset($raw['hb:counter'])) {
    echo Integer::binToInt($raw['hb:counter']), PHP_EOL;
}

$connection->close();

echo gettype(Integer::intToBin(5)), PHP_EOL;

$a = pack("J", 5);

var_dump($a);


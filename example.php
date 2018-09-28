<?php

require __DIR__ . '/vendor/autoload.php';

$connection = new \HelloBase\Connection();
$connection->connect();

print_r($connection->tables());

$table = $connection->table('hellobase');


$table->put('111', ['hb:id' => 'id:111', 'hb:name' => 'name:111']);

foreach ($table->scan() as $row => $columns) {
    var_dump($row, $columns);
}

//$table->increment('222', 'hb:counter', 5);

$raw = $table->row('222');

echo \HelloBase\Supports\Integer::binToInt($raw['hb:counter']), PHP_EOL;

$connection->close();

echo gettype(\HelloBase\Supports\Integer::intToBin(5)), PHP_EOL;

$a = pack("J", 5);

var_dump($a);


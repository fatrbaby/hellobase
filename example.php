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

$connection->close();


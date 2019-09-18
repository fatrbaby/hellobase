# Hellobase

> H(ello)base

## installation

`composer require fatrbaby/hellobase`

## usage

```php
use HelloBase\Connection;

$config = [
    'host' => 'localhost',
    'port' => '9090',
    'auto_connect' => false,
    'persist' => false,
    'debug_handler' => null,
    'send_timeout' => 1000000,
    'recv_timeout' => 1000000,
    'transport' => Connection::TRANSPORT_BUFFERED,
    'protocol' => Connection::PROTOCOL_BINARY_ACCELERATED,
];

$connection = new Connection($config);
$connection->connect();

# get tables 
$connection->tables();

# get table instance
$table = $connection->table('tableName');

# put data
$table->put('row-name', ['cf:foo' => 'bar']);

# get row
$table->row('row-name', ['column1', ...]);

# get rows
$table->rows(['row-name1', 'row-name2', ...], ['column1', ...]);

# increment 
$table->increment('row-name', 'column-name', int amount)

# scan
foreach($table->scan(<startRow>, <stopRow>, <['column1', ...]>, <['condition1', ...]>) as $row => $columns) {
    // do something
}

```

## run test

```shell script
# create hbase service by docker (docker-compose)
$ cd docker
$ docker-compose up -d

# create table in docker
$ docker exec -it hbase bash
$ hbase shell

> create 'hellobase', 'hb'
> list
> quit

$ exit 

# run test
cd ../
vendor/bin/phpunit
```

## TODO
more features


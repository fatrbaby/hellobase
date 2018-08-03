<?php

namespace HelloBase;

use Hbase\ColumnDescriptor;
use Hbase\HbaseClient;
use Hbase\IOError;
use HelloBase\Contracts\Connection as ConnectionContract;
use HelloBase\Contracts\Table;
use Thrift\Exception\TException;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;
use Thrift\Transport\TTransport;

class Connection implements ConnectionContract
{
    protected $config;

    /**
     * @var TSocket
     */
    protected $socket;

    /**
     * @var TTransport
     */
    protected $transport;

    /**
     * @var TTransport
     */
    protected $protocol;

    /**
     * @var HbaseClient
     */
    protected $client;

    /**
     * Connection constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function connect()
    {
        $config = $this->config;
        $this->socket = new TSocket($config['host'], $config['port'], $config['persist'], $config['debug_handler']);
        $this->socket->setSendTimeout($config['send_timeout']);
        $this->socket->setRecvTimeout($config['recv_timeout']);

        if ($config['transport'] == 'framed') {
            $this->transport = new TFramedTransport($this->socket);
        } else {
            $this->transport = new TBufferedTransport($this->socket);
        }

        if ($config['protocol'] == 'binary_accelerated') {
            $this->protocol = new TBinaryProtocolAccelerated($this->transport);
        } elseif ($config['protocol'] == 'binary') {
            $this->protocol = new TBinaryProtocol($this->transport);
        } else {
            $this->protocol = new TCompactProtocol($this->transport);
        }

        $this->client = new HbaseClient($this->protocol);

        if ($this->transport->isOpen()) {
            return;
        }

        try {
            $this->transport->open();
        } catch (TException $exception) {
            $this->socket->close();
        }
    }

    public function close()
    {
        if ($this->transport === null || !$this->transport->isOpen()) {
            return;
        }

        $this->transport->close();
        $this->transport = null;
        $this->socket->close();
    }

    public function table($name): Table
    {
        return new \HelloBase\Table($name, $this);
    }

    /**
     * get tables
     * @return array
     * @throws IOError
     */
    public function tables(): array
    {
        try {
            return $this->client->getTableNames();
        } catch (IOError $error) {
            throw $error;
        }
    }

    public function createTable($table, array $columnFamilies): bool
    {
        $descriptors = [];

        foreach ($columnFamilies as $column) {
            $descriptors[] = new ColumnDescriptor([
                'name' => trim($column, ':') . ':',
            ]);
        }

        $this->client->createTable($table, $descriptors);

        return true;
    }

    public function getClient(): HbaseClient
    {
        return $this->client;
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge([
            'host' => 'localhost',
            'port' => '9090',
            'persist' => false,
            'debug_handler' => null,
            'send_timeout' => 1000000,
            'recv_timeout' => 1000000,
            'transport' => 'buffered',
            'protocol' => 'binary_accelerated',
        ], $config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function __destruct()
    {
        $this->close();
    }
}

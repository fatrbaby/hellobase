<?php

namespace HelloBase;

use Exception;
use Hbase\ColumnDescriptor;
use Hbase\HbaseClient;
use Hbase\IOError;
use HelloBase\Contracts\Connection as ConnectionContract;
use HelloBase\Contracts\Table as TableContract;
use InvalidArgumentException;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;
use Thrift\Transport\TTransport;

class Connection implements ConnectionContract
{
    const TRANSPORT_BUFFERED = 'buffered';
    const TRANSPORT_FRAMED = 'framed';

    const PROTOCOL_BINARY = 'binary';
    const PROTOCOL_BINARY_ACCELERATED = 'binary_accelerated';
    const PROTOCOL_COMPACT = 'compact';

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
     * @var array
     */
    protected $tables = [];

    /**
     * @var bool
     */
    protected $autoConnect = false;

    /**
     * Connection constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->prepareConfig($config);

        if ($this->autoConnect) {
            $this->connect();
        }
    }

    public function connect()
    {
        $config = $this->config;
        $this->socket = new TSocket($config['host'], $config['port'], $config['persist'], $config['debug_handler']);
        $this->socket->setSendTimeout($config['send_timeout']);
        $this->socket->setRecvTimeout($config['recv_timeout']);

        switch ($config['transport']) {
            case self::TRANSPORT_BUFFERED:
                $this->transport = new TBufferedTransport($this->socket);
                break;
            case self::TRANSPORT_FRAMED:
                $this->transport = new TFramedTransport($this->socket);
                break;
            default:
                throw new InvalidArgumentException(sprintf(
                    "Invalid transport config '%s'",
                    $config['transport']
                ));
        }

        switch ($config['protocol']) {
            case self::PROTOCOL_BINARY_ACCELERATED:
                $this->protocol = new TBinaryProtocolAccelerated($this->transport);
                break;
            case self::PROTOCOL_BINARY:
                $this->protocol = new TBinaryProtocol($this->transport);
                break;
            case self::PROTOCOL_COMPACT:
                $this->protocol = new TCompactProtocol($this->transport);
               break;
            default:
                throw new InvalidArgumentException(sprintf(
                    "Invalid protocol config: '%s'",
                    $config['protocol']
                ));
        }

        $this->client = new HbaseClient($this->protocol);

        if ($this->transport->isOpen()) {
            return;
        }

        try {
            $this->transport->open();
        } catch (Exception $exception) {
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

    public function table($name): TableContract
    {
        return new Table($name, $this);
    }

    /**
     * get tables
     * @return array
     * @throws IOError
     */
    public function tables(): array
    {
        if ($this->tables) {
            return $this->tables;
        }

        try {
            return $this->tables = $this->client->getTableNames();
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

    public function prepareConfig(array $config)
    {
        $this->config = array_merge([
            'host' => 'localhost',
            'port' => '9090',
            'auto_connect' => false,
            'persist' => false,
            'debug_handler' => null,
            'send_timeout' => 1000000,
            'recv_timeout' => 1000000,
            'transport' => self::TRANSPORT_BUFFERED,
            'protocol' => self::PROTOCOL_BINARY_ACCELERATED,
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

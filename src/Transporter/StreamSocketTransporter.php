<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace MicroTool\HyperfRpcClient\Transporter;

use MicroTool\HyperfRpcClient\Backoff;
use MicroTool\HyperfRpcClient\Exception\ClientException;
use MicroTool\HyperfRpcClient\Exception\ConnectionException;
use MicroTool\HyperfRpcClient\Exception\ExceptionThrower;
use MicroTool\HyperfRpcClient\Exception\RecvFailedException;
use MicroTool\HyperfRpcClient\Exception\Throwable;

class StreamSocketTransporter extends AbstractTransporter
{
    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     * @var null|resource
     */
    protected $client;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * @var bool
     */
    protected $isConnected = false;

    public function __construct($host = '', $port = 9501, $timeout = 1.0)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function send($data)
    {
        $this->connect();
        fwrite($this->client, $data);
    }

    public function recv()
    {
        try {
            $result = $this->receive();
        } catch (Throwable $exception) {
            $this->close();
            throw $exception;
        }

        return $result;
    }

    function retry($times, callable $callback, $sleep = 0)
    {
        $backoff = new Backoff($sleep);
        beginning:
        try {
            return $callback();
        } catch (Throwable $e) {
            if (--$times < 0) {
                throw $e;
            }
            $backoff->sleep();
            goto beginning;
        }
    }

    public function receive()
    {
        $buf     = '';
        $timeout = 1;

        stream_set_blocking($this->client, false);

        // The maximum number of retries is 12, and 1000 microseconds is the minimum waiting time.
        // The waiting time is doubled each time until the server writes data to the buffer.
        // Usually, the data can be obtained within 1 microsecond.
        $result = $this->retry(12, function () use (&$buf, &$timeout) {
            $read   = [$this->client];
            $write  = null;
            $except = null;
            while (stream_select($read, $write, $except, $timeout)) {
                foreach ($read as $r) {
                    $res = fread($r, 8192);
                    if (feof($r)) {
                        return new ExceptionThrower(new ConnectionException('Connection was closed.'));
                    }
                    $buf .= $res;
                }
            }
            if (!$buf) {
                $timeout *= 2;

                throw new RecvFailedException('No data was received');
            }

            return $buf;
        });

        if ($result instanceof ExceptionThrower) {
            throw $result->getThrowable();
        }

        return $result;
    }

    protected function getTarget()
    {
        if ($this->getLoadBalancer()) {
            $node = $this->getLoadBalancer()->select();
        } else {
            $node = $this;
        }
        if (!$node->host || !$node->port) {
            throw new ClientException(sprintf('Invalid host %s or port %s.', $node->host, $node->port));
        }

        return [$node->host, $node->port];
    }

    protected function connect()
    {
        if ($this->isConnected) {
            return;
        }
        if ($this->client) {
            fclose($this->client);
            unset($this->client);
        }

        list($host, $port) = $this->getTarget();

        $client = stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $this->timeout);
        if ($client === false) {
            //throw new ConnectionException(sprintf('[%d] %s', $errno, $errstr));
        }

        $this->client      = $client;
        $this->isConnected = true;
    }

    protected function close()
    {
        if ($this->client) {
            fclose($this->client);
            $this->client = null;
        }

        $this->isConnected = false;
    }
}

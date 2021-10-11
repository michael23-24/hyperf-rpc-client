<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace MicroTool\HyperfRpcClient\NodeSelector;


class NodeSelector
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
     * @var array
     */
    public $config;

    public function __construct($host = '127.0.0.1', $port = 8500, array $config = [])
    {
        $this->host   = $host;
        $this->port   = $port;
        $this->config = $config;
    }


}

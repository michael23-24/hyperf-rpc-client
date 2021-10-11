<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace MicroTool\HyperfRpcClient;

class ProtocolManager
{
    const DATA_FORMATTER = 'df';

    const PATH_GENERATOR = 'pg';

    const TRANSPORTER = 't';

    const PACKER = 'p';

    const NODE_SELECTOR = 'ns';

    /**
     * @var array
     */
    protected static $protocols = [];

    public static function getProtocol($protocolName)
    {
        return static::$protocols[$protocolName] ?: [];
    }

    public static function isProtocolRegistered($protocolName)
    {
        return isset(static::$protocols[$protocolName]);
    }

    public static function register($protocolName, array $metadatas)
    {
        static::$protocols[$protocolName] = $metadatas;
    }

    public static function deregister($protocolName)
    {
        unset(static::$protocols[$protocolName]);
    }
}

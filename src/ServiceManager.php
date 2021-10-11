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


use MicroTool\HyperfRpcClient\Exception\ClientException;

class ServiceManager
{
    const NODES = 'nodes';

    /**
     * @var array
     */
    protected static $services = [];

    public static function getService($service, $protocol)
    {
        return static::$services[static::buildKey($service, $protocol)] ?: [];
    }

    public static function isServiceRegistered($service, $protocol)
    {
        return isset(static::$services[static::buildKey($service, $protocol)]);
    }

    public static function register($service, $protocol, array $metadata = [])
    {
        if (!ProtocolManager::isProtocolRegistered($protocol)) {
            throw new ClientException(sprintf('The protocol %s does not register to %s yet.', ProtocolManager::class, $protocol));
        }
        static::$services[static::buildKey($service, $protocol)] = $metadata;
    }

    public static function deregister($service, $protocol)
    {
        unset(static::$services[static::buildKey($service, $protocol)]);
    }

    private static function buildKey($service, $protocol)
    {
        return $service . '@' . $protocol;
    }
}

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


abstract class AbstractTransporter implements TransporterInterface
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
     * @var null|LoadBalancerInterface
     */
    protected $loadBalancer;

    public function getLoadBalancer()
    {
        return $this->loadBalancer;
    }

    public function setLoadBalancer($loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
        return $this;
    }
}

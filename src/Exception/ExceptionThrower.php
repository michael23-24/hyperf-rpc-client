<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace MicroTool\HyperfRpcClient\Exception;


final class ExceptionThrower
{
    /**
     * @var Throwable
     */
    private $throwable;

    public function __construct($throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable()
    {
        return $this->throwable;
    }
}

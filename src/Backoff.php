<?php
// +----------------------------------------------------------------------
// | 注释
// +----------------------------------------------------------------------
// | Copyright (c) 义幻科技 http://www.mobimedical.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Michael23
// +----------------------------------------------------------------------
// | date: 2021-10-11
// +----------------------------------------------------------------------
namespace MicroTool\HyperfRpcClient;

class Backoff
{
    /**
     * Max backoff.
     */
    const CAP = 60000; // 1 minute

    /**
     * @var int
     */
    private $firstMs;

    /**
     * Backoff interval.
     * @var int
     */
    private $currentMs;

    /**
     * @param int the first backoff in milliseconds
     */
    public function __construct($firstMs = 0)
    {
        if ($firstMs < 0) {
            throw new \InvalidArgumentException(
                'first backoff interval must be greater or equal than 0'
            );
        }

        if ($firstMs > Backoff::CAP) {
            throw new \InvalidArgumentException(
                sprintf(
                    'first backoff interval must be less or equal than %d milliseconds',
                    self::CAP
                )
            );
        }

        $this->firstMs   = $firstMs;
        $this->currentMs = $firstMs;
    }

    /**
     * Sleep until the next execution.
     */
    public function sleep()
    {
        if ($this->currentMs === 0) {
            return;
        }

        usleep($this->currentMs * 1000);

        // update backoff using Decorrelated Jitter
        // see: https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/
        $this->currentMs = rand($this->firstMs, $this->currentMs * 3);

        if ($this->currentMs > self::CAP) {
            $this->currentMs = self::CAP;
        }
    }

    /**
     * Get the next backoff for logging, etc.
     * @return int next backoff
     */
    public function nextBackoff()
    {
        return $this->currentMs;
    }
}

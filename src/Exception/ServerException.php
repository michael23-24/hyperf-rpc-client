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


class ServerException extends JetException
{
    /**
     * @var array
     */
    protected $error;

    public function __construct(array $error = [], Throwable $previous = null)
    {
        $code = $error['code'] ?: 0;
        $message = $error['message'] ?: 'Server Error';

        $this->error = $error;
        parent::__construct($message, $code, $previous);
    }

    public function getError()
    {
        return $this->error;
    }
}

<?php

namespace MicroTool\HyperfRpcClient\DataFormatter;

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */


use MicroTool\HyperfRpcClient\Exception\Throwable;

class DataFormatter implements DataFormatterInterface
{
    protected $params = null;

    public function __construct()
    {
        $this->params = func_get_args();
    }

    public function formatRequest($data)
    {
        list($path, $params, $id) = $data;
        return [
            'jsonrpc' => '2.0',
            'method'  => $path,
            'params'  => $params,
            'id'      => $id,
            'data'    => $this->params ? $this->params[0] : null,
        ];
    }

    public function formatResponse($data)
    {
         list($id, $result)= $data;
        return [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'result'  => $result,
        ];
    }

    public function formatErrorResponse($data)
    {
        list($id, $code, $message, $data) = $data;

        if (isset($data) && $data instanceof Throwable) {
            $data = [
                'class' => get_class($data),
                'code'  => $data->getCode(),
                'msg'   => $data->getMessage(),
            ];
        }
        return [
            'jsonrpc' => '2.0',
            'id'      => $id ?: null,
            'error'   => [
                'code' => $code,
                'msg'  => $message,
                'data' => $data,
            ],
        ];
    }
}

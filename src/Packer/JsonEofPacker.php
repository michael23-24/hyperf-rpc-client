<?php

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace MicroTool\HyperfRpcClient\Packer;


class JsonEofPacker implements PackerInterface
{
    /**
     * @var string
     */
    protected $eof;

    public function __construct($eof = "\r\n")
    {
        $this->eof = $eof;
    }

    public function pack($data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        return $data . $this->eof;
    }

    public function unpack($data)
    {
        return json_decode($data, true);
    }
}

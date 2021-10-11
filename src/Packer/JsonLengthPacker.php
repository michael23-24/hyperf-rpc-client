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


class JsonLengthPacker implements PackerInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var array
     */
    protected $defaultOptions = [
        'package_length_type' => 'N',
        'package_body_offset' => 4,
    ];

    public function __construct(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);

        $this->type   = $options['package_length_type'];
        $this->length = $options['package_body_offset'];
    }

    public function pack($data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        return pack($this->type, strlen($data)) . $data;
    }

    public function unpack($data)
    {
        $data = substr($data, $this->length);
        if (!$data) {
            return null;
        }
        return json_decode($data, true);
    }
}

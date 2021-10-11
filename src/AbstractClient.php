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


use MicroTool\HyperfRpcClient\DataFormatter\DataFormatter;
use MicroTool\HyperfRpcClient\DataFormatter\DataFormatterInterface;
use MicroTool\HyperfRpcClient\Exception\RecvFailedException;
use MicroTool\HyperfRpcClient\Exception\ServerException;
use MicroTool\HyperfRpcClient\Packer\PackerInterface;
use MicroTool\HyperfRpcClient\PathGenerator\PathGenerator;
use MicroTool\HyperfRpcClient\Traits\DataFormateTrait;
use MicroTool\HyperfRpcClient\Transporter\TransporterInterface;
use MicroTool\HyperfRpcClient\PathGenerator\PathGeneratorInterface;

abstract class AbstractClient
{
    use DataFormateTrait;

    /**
     * @var null|resource
     */
    protected $client;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var PackerInterface
     */
    protected $packer;

    /**
     * @var DataFormatterInterface
     */
    protected $dataFormatter;

    /**
     * @var PathGeneratorInterface
     */
    protected $pathGenerator;

    /**
     * @var TransporterInterface
     */
    protected $transporter;

    public function __construct($service, TransporterInterface $transporter, PackerInterface $packer, DataFormatterInterface $dataFormatter = null, PathGeneratorInterface $pathGenerator = null)
    {
        $this->service     = $service;
        $this->packer      = $packer;
        $this->transporter = $transporter;
        is_null($dataFormatter) && $dataFormatter = new DataFormatter();
        $this->dataFormatter = $dataFormatter;
        is_null($pathGenerator) && $pathGenerator = new PathGenerator();
        $this->pathGenerator = $pathGenerator;
    }

    public function __call($name, $arguments)
    {
        try {
            $path = $this->pathGenerator->generate($this->service, $name);
            $data = $this->dataFormatter->formatRequest([$path, $arguments, uniqid()]);
            $this->transporter->send($this->packer->pack($data));
            $ret = $this->transporter->recv();

            if (!is_string($ret)) {
                throw new RecvFailedException();
            }

            $data = $this->packer->unpack($ret);

            if (array_key_exists('result', $data)) {
                return $data['result'];
            }

            throw new ServerException($data['error'] ?: []);


        } catch (\Exception $exception) {
            return $this->formateData(0, $exception->getMessage(), $exception->__toString());
        }

    }
}

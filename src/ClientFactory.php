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

use MicroTool\HyperfRpcClient\DataFormatter\DataFormatterInterface;
use MicroTool\HyperfRpcClient\Exception\ClientException;

use MicroTool\HyperfRpcClient\NodeSelector\NodeSelector;
use MicroTool\HyperfRpcClient\Packer\PackerInterface;
use MicroTool\HyperfRpcClient\PathGenerator\PathGeneratorInterface;
use MicroTool\HyperfRpcClient\ProtocolManager as PM;
use MicroTool\HyperfRpcClient\ServiceManager as SM;
use MicroTool\HyperfRpcClient\Transporter\AbstractTransporter;
use MicroTool\HyperfRpcClient\Transporter\TransporterInterface;


class ClientFactory
{
    public function create($service, $protocol)
    {
        list($transporter, $packer, $dataFormatter, $pathGenerator, $nodeSelector) = $this->protocolComponentGenerate($protocol);

        $this->selectNodesForTransporter($transporter, $nodeSelector, $service, $protocol);

        return new AnonymityClass($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }

    /**
     * @param AbstractTransporter $transporter
     * @param mixed $nodeSelector
     */
    protected function selectNodesForTransporter(TransporterInterface $transporter, $nodeSelector,  $service,  $protocol)
    {

        $randomNodes = $this->getRandomNodes($service, $protocol);
        list($transporter->host, $transporter->port) = $randomNodes;
    }

    protected function getRandomNodes($service, $protocol)
    {
        $nodeData = SM::getService($service, $protocol)[SM::NODES] ?: [];

        if (!count($nodeData)) {
            return $nodeData;
        }

        $key = array_rand($nodeData);

        return $nodeData[$key];
    }


    protected function protocolComponentGenerate($protocol)
    {
        $protocolMetadata = PM::getProtocol($protocol);
        $transporter      = $protocolMetadata[PM::TRANSPORTER] ?: null;
        $packer           = $protocolMetadata[PM::PACKER] ?: null;
        $dataFormatter    = $protocolMetadata[PM::DATA_FORMATTER] ?: null;
        $pathGenerator    = $protocolMetadata[PM::PATH_GENERATOR] ?: null;
        $nodeSelector     = $protocolMetadata[PM::NODE_SELECTOR] ?: null;

        if (!$transporter instanceof TransporterInterface) {
            throw new ClientException(sprintf('The protocol of %s transporter is invalid.', $protocol));
        }

        if (!$packer instanceof PackerInterface) {
            throw new ClientException(sprintf('The protocol of %s packer is invalid.', $protocol));
        }

        if (!$dataFormatter instanceof DataFormatterInterface) {
            throw new ClientException(sprintf('The protocol of %s is data formatter invalid.', $protocol));
        }

        if (!$pathGenerator instanceof PathGeneratorInterface) {
            throw new ClientException(sprintf('The protocol of %s is path generator invalid.', $protocol));
        }

        return [$transporter, $packer, $dataFormatter, $pathGenerator, $nodeSelector];
    }
}


class AnonymityClass extends AbstractClient
{
    public function __construct($service, TransporterInterface $transporter, PackerInterface $packer, DataFormatterInterface $dataFormatter = null, PathGeneratorInterface $pathGenerator = null)
    {
        parent::__construct($service, $transporter, $packer, $dataFormatter, $pathGenerator);
    }
}

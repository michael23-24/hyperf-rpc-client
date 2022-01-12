<?php
// +----------------------------------------------------------------------
// | 注册服务
// +----------------------------------------------------------------------
// | Copyright (c) 义幻科技 http://www.mobimedical.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Michael23
// +----------------------------------------------------------------------
// | date: 2021-10-11
// +----------------------------------------------------------------------
namespace MicroTool\HyperfRpcClient;

use MicroTool\HyperfRpcClient\DataFormatter\DataFormatter;
use MicroTool\HyperfRpcClient\Packer\JsonEofPacker;
use MicroTool\HyperfRpcClient\PathGenerator\PathGenerator;
use MicroTool\HyperfRpcClient\Transporter\GuzzleHttpTransporter;
use MicroTool\HyperfRpcClient\Transporter\StreamSocketTransporter;
use MicroTool\Nacos\NacosClient;

class RegisterService
{
    protected $nacosClient = null;

    protected $publicParams = [];

    protected $protocol = null;

    public function __construct($baseUri, $username = '', $password = '', $publicParams = [], $recvTimeout = 1, $protocol = 'jsonrpc')
    {
        if ($this->nacosClient == null) {
            $this->nacosClient = new NacosClient([
                'base_uri'      => $baseUri,
                'username'      => $username,
                'password'      => $password,
                'guzzle_config' => [
                    'headers' => [
                        'charset' => 'UTF-8',
                    ],
                ],
            ]);
        }
        $this->publicParams = $publicParams;
        $this->protocol     = $protocol;

        $transporter = null;
        if ($this->protocol == 'jsonrpc') {
            $transporter = new StreamSocketTransporter($recvTimeout);
        } elseif ($this->protocol == 'jsonrpc-http') {
            $transporter = new GuzzleHttpTransporter(null, null, ['timeout' => $recvTimeout]);
        }

        ProtocolManager::register($this->protocol, [
            ProtocolManager::TRANSPORTER    => $transporter,
            ProtocolManager::PACKER         => new JsonEofPacker(),
            ProtocolManager::PATH_GENERATOR => new PathGenerator(),
            ProtocolManager::DATA_FORMATTER => new DataFormatter($publicParams),
            ProtocolManager::NODE_SELECTOR  => null,
        ]);
    }

    /**
     * 获取服务地址
     * @param $serviceName 服务名称
     * @param $groupName 命名空间名称
     * @param $namespaceId 命名空间ID
     * @return array
     */
    public function register($serviceName, $groupName, $namespaceId)
    {
        $response    = $this->nacosClient->lists($serviceName, ['groupName' => $groupName, 'namespaceId' => $namespaceId]);
        $result      = json_decode((string)$response->getBody(), true);
        $serviceNode = [];
        foreach ($result['hosts'] as $service) {
            $serviceNode[] = [
                //$service['ip'],
               'localhost',
                $service['port'],
            ];
        }

        ServiceManager::register($serviceName, $this->protocol, [
            ServiceManager::NODES => $serviceNode
        ]);

        $clientFactory = new ClientFactory();
        return $clientFactory->create($serviceName, $this->protocol);

    }
}

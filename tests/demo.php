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
error_reporting(E_ALL);
require '../vendor/autoload.php';
spl_autoload_register(function ($class) { // class = os\Linux

    $vendor = substr($class, 0, strpos($class, '\\') + strlen('HyperfRpcClient') + 1); // 取出顶级命名空间[app]

    $relpath = substr($class, strlen($vendor) + 1, strlen($class)) . ".php"; // 相对路径[/view/news]

    $file = '../src/' . $relpath;

    if ($vendor == 'MicroTool\\HyperfRpcClient') {
        var_dump($file);
        if (file_exists($file)) {
            var_dump($file);
            include $file;
        }
    }

});
$serviceName  = 'test';
$groupName    = 'test';
$namespaceId  = 'f6525dcf-8a03-4bb4-930a-b3e6bd510a5b';
$timestamp    = time();
$sign         = md5('test' . $timestamp);
$publicParams = ['test' => 1, 'test' => 1001, 'sign' => $sign, 'timestamp' => $timestamp];
$method       = 'test';

use MicroTool\HyperfRpcClient\RegisterService;


$service = new RegisterService('http://localhost:8848', 'nacos', 'nacos', $publicParams, 12);
$client  = $service->register($serviceName, $groupName, $namespaceId);
$time    = time();
var_dump($client->$method('2021-10-08 15:25:00', '2021-10-09 15:25:00'));
$time2 = time();
var_dump(($time2 - $time));

exit;

use MicroTool\HyperfRpcClient\ClientFactory;
use MicroTool\HyperfRpcClient\DataFormatter\DataFormatter;
use MicroTool\HyperfRpcClient\Packer\JsonEofPacker;
use MicroTool\HyperfRpcClient\PathGenerator\PathGenerator;
use MicroTool\HyperfRpcClient\ProtocolManager;
use MicroTool\HyperfRpcClient\ServiceManager;
use MicroTool\HyperfRpcClient\Transporter\StreamSocketTransporter;


$application = new \MicroTool\Nacos\NacosClient([
    'base_uri'      => 'http://localhost:8848',
    'username'      => 'nacos',
    'password'      => 'nacos',
    'guzzle_config' => [
        'headers' => [
            'charset' => 'UTF-8',
        ],
    ],
]);
$response    = $application->lists($serviceName, ['groupName' => $groupName, 'namespaceId' => $namespaceId]);
$result      = json_decode((string)$response->getBody(), true);
$serviceNode = [];
foreach ($result['hosts'] as $service) {
    $serviceNode[] = [
        //$service['ip'],
        'localhost',
        $service['port'],
    ];
}


ProtocolManager::register('jsonrpc', [
    ProtocolManager::TRANSPORTER    => new StreamSocketTransporter(),
    ProtocolManager::PACKER         => new JsonEofPacker(),
    ProtocolManager::PATH_GENERATOR => new PathGenerator(),
    ProtocolManager::DATA_FORMATTER => new DataFormatter($publicParams),
]);
ServiceManager::register($serviceName, 'jsonrpc', [
    ServiceManager::NODES => $serviceNode
]);

$ClientFactory = new ClientFactory();
$client        = $ClientFactory->create($serviceName, 'jsonrpc');
$time          = time();
var_dump($client->$method('2021-10-08 15:25:00', '2021-10-09 15:25'));
$time2 = time();
var_dump(($time2 - $time));



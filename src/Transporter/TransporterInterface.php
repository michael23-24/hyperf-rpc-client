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
namespace MicroTool\HyperfRpcClient\Transporter;

interface TransporterInterface
{
    public function send( $data);

    public function recv();

    public function getLoadBalancer();

    public function setLoadBalancer( $loadBalancer);
}

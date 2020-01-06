<?php

namespace Jcsp\WsCluster\Register;

use Jcsp\WsCluster\Cluster;
use Jcsp\WsCluster\StateInterface;
use Swoft\Bean\BeanFactory;
use Swoft\Stdlib\Helper\StringHelper;

class ClusterRegister
{
    /**
     * 注册
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public static function registerServer(): void
    {
        /** @var StateInterface $stateCluster */
        $stateCluster = BeanFactory::getBean(Cluster::STATE);
        $stateCluster->discover();
    }
}

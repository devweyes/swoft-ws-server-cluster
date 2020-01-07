<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

use Jcsp\WsCluster\Helper\Tool;
use Swoft\Bean\BeanFactory;
use Swoft\Stdlib\Helper\StringHelper;

/**
 * Class ClusterManager
 * @package Jcsp\WsCluster
 */
class ClusterManager
{
    /**
     * @var StateInterface
     */
    private $state;
    /**
     * @var string
     */
    private $serverIdPrefix = 'server_';
    /**
     * @var string
     */
    private $serverId;

    /**
     * init
     * @return void
     */
    public function init(): void
    {
        if (!$this->serverId) {
            $this->serverId = $this->serverIdPrefix . StringHelper::random();
        }
    }

    /**
     * get serverid.
     * @return string
     */
    public function getServerId(): string
    {
        return $this->serverId;
    }
    /**
     * get serverids.
     * @return array
     */
    public function getState(): StateInterface
    {
        return BeanFactory::getBean(Cluster::STATE);
    }
    /**
     * get serverids.
     * @return array
     */
    public function getServerIds(): array
    {
        return $this->getState()->getServerIds();
    }

    /**
     * @return string
     */
    public function generateUid(): string
    {
        return Tool::uniqidReal();
    }
}

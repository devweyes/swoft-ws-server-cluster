<?php

namespace Jcsp\WsCluster;

use Swoft\Bean\BeanFactory;

abstract class AbstractState implements StateInterface
{
    /**
     * register uid
     * @param int $fdid
     * @param string|null $uid
     */
    abstract public function register(int $fdid, string $uid = null): void;

    /**
     * logout
     * @param int $fdid
     */
    abstract public function logout(int $fdid): void;

    /**
     * transport message
     * @param string $message
     * @param null $targetUid
     * @param null $targetFdid
     * @param int|null $originUid
     * @param int|null $originFdid
     * @return bool
     */
    abstract public function transport(
        string $message,
        $targetUid = null,
        $targetFdid = null,
        int $originUid = null,
        int $originFdid = null
    ): bool;

    /**
     * shutdown
     */
    abstract public function shutdown(): void;

    /**
     * discover
     */
    abstract public function discover(): void;

    /**
     * @return array
     */
    abstract public function getServerIds(): array;

    /**
     * get Server id
     */
    public function getServerId(): string
    {
        return $this->getManager()->getServerId();
    }

    /**
     * @return ClusterManager
     */
    protected function getManager(): ClusterManager
    {
        return BeanFactory::getBean(Cluster::MANAGER);
    }
}

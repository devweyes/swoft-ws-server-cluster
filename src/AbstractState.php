<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

use Swoft\Bean\BeanFactory;

abstract class AbstractState implements StateInterface
{
    /**
     * register uid
     * @param int $fdid
     * @param string|null $uid
     */
    abstract public function register(int $fdid, string $uid = null): bool;

    /**
     * logout
     * @param int $fdid
     */
    abstract public function logout(int $fdid): bool;

    /**
     * transport message
     * @param string $message
     * @param null $targetUid
     * @param null $targetFdid
     * @param int|null $originUid
     * @param int|null $originFdid
     * @return bool
     */
    abstract public function transport(string $message, $uid = null): bool;

    /**
     * @param string $message
     * @param mixed ...$uid
     * @return bool
     */
    abstract public function transportToUid(string $message, $uid): bool;

    /**
     * @param string $message
     * @return bool
     */
    abstract public function transportToAll(string $message): bool;
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
     * send to some
     * @param string $message
     * @param mixed ...$uid
     * @return int
     */
    public function transportToFd(string $message, ...$uid): int
    {
        return server()->sendToSome($message, (array)$fd);
    }

    /**
     * @return ClusterManager
     */
    protected function getManager(): ClusterManager
    {
        return BeanFactory::getBean(Cluster::MANAGER);
    }
}

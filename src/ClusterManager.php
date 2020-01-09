<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

use BadMethodCallException;
use Jcsp\WsCluster\Middleware\AbstractOpenMiddleware;
use Jcsp\WsCluster\Middleware\AbstracHandshakeMiddleware;
use Jcsp\WsCluster\Helper\Tool;
use Swoft\Bean\BeanFactory;
use Swoft\Stdlib\Helper\StringHelper;

/**
 * Class ClusterManager
 * @package Jcsp\WsCluster
 * @method  bool register(int $fdid, string $uid = null)
 * @method  bool logout(int $fdid)
 * @method  bool transport(string $message, $uid = null)
 * @method  bool transportToUid(string $message, ...$uid)
 * @method  bool transportToAll(string $message)
 * @method void shutdown()
 * @method  void discover()
 * @method  array getServerIds()
 */
class ClusterManager
{
    /**
     * @var StateInterface
     */
    private $state;
    /**
     * @var AbstractOpenMiddleware[]
     */
    private $onOpenMiddleware = [];
    /**
     * @var AbstracHandshakeMiddleware[]
     */
    private $onHandshakeMiddleware = [];
    /**
     * @var string
     */
    private $serverIdPrefix = 'server_';
    /**
     * @var string
     */
    private $serverId;
    /**
     * @var int 
     */
    private $heartbeat = 60;

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
     * @return string
     */
    public function generateUid(): string
    {
        return Tool::uniqidReal();
    }

    /**
     * @return AbstractOpenMiddleware[]
     */
    public function getOnOpenMiddleware()
    {
        return $this->onOpenMiddleware;
    }

    /**
     * @return AbstracHandshakeMiddleware[]
     */
    public function getOnHandshakeMiddleware()
    {
        return $this->onHandshakeMiddleware;
    }

    /**
     * @return int
     */
    public function getHeartbeat(): int
    {
        return $this->heartbeat;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->getState(), $name)) {
            throw new BadMethodCallException(sprintf('method:%s not found', $name));
        }
        return $this->getState()->$name(...$arguments);
    }
}

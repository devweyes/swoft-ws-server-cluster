<?php

namespace Jcsp\WsCluster\State;

use Jcsp\Queue\Queue;
use Jcsp\WsCluster\AbstractState;
use Jcsp\WsCluster\Cluster;
use Jcsp\WsCluster\ClusterManager;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\Redis\Pool;
use Swoft\Serialize\Contract\SerializerInterface;
use Swoft\Serialize\PhpSerializer;
use Swoft\Stdlib\Helper\Arr;

class RedisState extends AbstractState
{
    /**
     * @var Pool
     */
    private $redis;
    /**
     * @var string
     */
    private $prefix = 'swoft_ws_server_cluster';
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * register uid
     * @param int $fdid
     * @param string|null $uid
     */
    public function register(int $fdid, string $uid = null): bool
    {
        if (!$uid) {
            $uid = $this->getManager()->generateUid();
        }
        $value = $fdid . '|' . $this->getServerId();
        $value2 = $uid . '|' . $this->getServerId();
        return $this->redis->eval(
            LuaScripts::register(),
            [
                $this->getPrefix() . ':user',
                $this->getPrefix() . $this->getServerId() . ':server',
                (string)$uid,
                (string)$fdid,
                $value,
                $value2
            ],
            2
        );
    }

    /**
     * logout
     * @param int $fdid
     */
    public function logout(int $fdid): bool
    {
        return $this->redis->eval(
            LuaScripts::register(),
            [
                $this->getPrefix() . ':user',
                $this->getPrefix() . $this->getServerId() . ':server',
                (string)$fdid
            ],
            2
        );
    }

    /**
     * @param string $message
     * @param null $uid
     * @return bool
     */
    public function transport(string $message, $uid = null): bool
    {
        if (is_null($uid)) {
            return $this->transportToAll($message);
        }
        return $this->transportToUid($message, (array)$uid);
    }

    /**
     * @param string $message
     * @return bool
     */
    public function transportToAll(string $message): bool
    {
    	d('发消息给所有', $this->getServerIds(),$this->getServerId());
        foreach ($this->getServerIds() as $serverId) {
            $queue = $this->getPrefix() . ':message:' . $serverId;
            Queue::bind($queue)->push([$message, null]);
        }
        return true;
    }

    /**
     * @param string $message
     * @param mixed ...$uid
     * @return bool
     */
    public function transportToUid(string $message, $uid): bool
    {
        $server = [];
        foreach ((array)$uid as $id) {
            if ($value = $this->redis->hGet($this->getPrefix() . ':user', (string)$id)) {
                $value = explode('|', $value);
                if (!is_array($value) || count($value) !== 2) {
                    continue;
                }
                [$fd, $serverId] = $value;
                $server[$serverId][] = (int)$fd;
            }
        }
        d($server, $this->getServerId(), 222);
        //send queue
        foreach (Arr::except($server, $this->getServerId()) as $key => $fds) {
            $queue = $this->getPrefix() . ':message:' . $key;
            Queue::bind($queue)->push([$message, $fds]);
        }

        //send local fdid
        $fds = Arr::get($server, $this->getServerId());
        //TODO send queue local
        foreach ($fds as $fd) {
            server()->sendTo($fd, $message);
        }

        //TODO event
        return true;
    }

    /**
     * shutdown
     */
    public function shutdown(): void
    {
        $this->redis->sRem($this->prefix . ':serverids', $this->getServerId());
    }

    /**
     * discover
     */
    public function discover(): void
    {
        $this->redis->sAdd($this->prefix . ':serverids', $this->getServerId());
    }

    /**
     * @return array
     */
    public function getServerIds(): array
    {
        return $this->redis->sMembers($this->prefix . ':serverids') ?: [];
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        if (!$this->serializer) {
            $this->serializer = new PhpSerializer();
        }

        return $this->serializer;
    }
}

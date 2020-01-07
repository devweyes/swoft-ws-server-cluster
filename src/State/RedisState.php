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
     * register uid
     * @param int $fdid
     * @param string|null $uid
     */
    public function register(int $fdid, string $uid = null): bool
    {
        $value = [$fdid, $this->getServerId()];
        if (!$uid) {
            $uid = $this->getManager()->generateUid();
        }
        return $this->redis->eval(
            LuaScripts::register(),
            [
                $this->getPrefix() . ':user',
                $this->getPrefix() . $this->getServerId() . ':server',
                (string)$uid,
                (string)$fdid,
                $this->getSerializer()->serialize($value)
            ], 2);
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
            ], 2);
    }

    /**
     * transport message
     * @param string $message
     * @param null $targetUid
     * @param null $targetFdid
     * @param int|null $originUid
     * @param int|null $originFdid
     * @return bool
     */
    public function transport(
        string $message,
        $targetUid = null,
        $targetFdid = null,
        int $originUid = null,
        int $originFdid = null
    ): bool
    {
        //transport to all
        if (!$targetUid && !$targetFdid) {
            foreach ($this->getServerIds() as $serverId) {
                $queue = $this->getPrefix() . ':message:' . $serverId;
                Queue::bind($queue)->push([$message, null]);
            }
            return true;
        }

        //transport to uid
        if ($targetUid) {
            foreach ((array)$targetUid as $uid) {
                if (!is_string($uid)) {
                    continue;
                }
                if ($value = $this->redis->hGet($this->getPrefix() . ':user', $uid)) {
                    $value = $this->getSerializer()->unserialize($value);
                    if (!is_array($value) || count($value) !== 2) {
                        continue;
                    }
                    $server[$value[1]][] = $value[2];
                }
            }
        }
        //transport to fdid .local
        if ($targetFdid) {
            foreach ((array)$targetFdid as $fdid) {
                $server[$this->getServerId()][] = $fdid;
            }
        }

        //send queue
        foreach (Arr::except($server, $this->getServerId()) as $key => $fdidArr) {
            $queue = $this->getPrefix() . ':message:' . $key;
            Queue::bind($queue)->push($this->getSerializer()->serialize([$message, $fdidArr]));
        }

        //send local fdid
        $fdidArr = Arr::get($server, $this->getServerId());
        //TODO send queue local
        foreach ($fdidArr as $fd) {
            server()->push($fd, $message);
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

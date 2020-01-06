<?php

namespace Jcsp\WsCluster\State;

use Jcsp\WsCluster\AbstractState;
use Jcsp\WsCluster\Cluster;
use Jcsp\WsCluster\ClusterManager;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\Redis\Pool;
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
    private $uidHashMapPrefix = 'swoft_ws_server_cluster:uid';
    /**
     * @var string
     */
    private $serverIdHashMapPrefix = 'swoft_ws_server_cluster:server_';
    /**
     * @var string
     */
    private $messageQueuePrefix = 'swoft_ws_server_cluster:message_';
    /**
     * @var string
     */
    private $serverIdsPrefix = 'swoft_ws_server_cluster:serverids';
    /**
     * register uid
     * @param int $fdid
     * @param string|null $uid
     */
    public function register(int $fdid, string $uid = null): void
    {
        $value = [$fdid, $this->getServerId()];
        if (!$uid) {
            $uid = $this->getManager()->generateUid();
        }
        $this->redis->hSet($this->uidHashMapPrefix, $uid, $value);
        $this->redis->hSet($this->serverIdHashMapPrefix . $this->getServerId(), $fdid, $uid);
    }

    /**
     * logout
     * @param int $fdid
     */
    public function logout(int $fdid): void
    {
        $uid = $this->redis->hGet($this->serverIdHashMapPrefix . $this->getServerId(), $fdid);
        if ($uid) {
            $this->redis->hDel($this->serverIdHashMapPrefix . $this->getServerId(), $fdid);
            $this->redis->hDel($this->uidHashMapPrefix, $uid);
        }
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
    ): bool {
        //transport to all
        if (!$targetUid && !$targetFdid) {
            foreach ($this->getServerIds() as $serverId) {
                //TODO send queue
            }

            return true;
        }

        //transport to uid
        if ($targetUid) {
            foreach ((array)$targetUid as $uid) {
                if (!is_string($uid)) {
                    continue;
                }
                if ($value = $this->redis->hGet($this->uidHashMapPrefix, $uid)) {
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
        foreach (Arr::except($server, $this->getServerId()) as $server => $fdidArr) {
            //TODO send queue
        }

        //send local fdid
        $fdidArr = Arr::get($server, $this->getServerId());
        //TODO send queue local


        //TODO event
        return true;
    }

    /**
     * shutdown
     */
    public function shutdown(): void
    {
        $this->redis->sRem($this->serverIdsPrefix, $this->getServerId());
    }
    /**
     * discover
     */
    public function discover(): void
    {
        $this->redis->sAdd($this->serverIdsPrefix, $this->getServerId());
    }

    /**
     * @return array
     */
    public function getServerIds(): array
    {
        return $this->redis->sMembers($this->serverIdsPrefix) ?: [];
    }
}

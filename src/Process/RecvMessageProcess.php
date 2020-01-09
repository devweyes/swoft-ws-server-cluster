<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Process;

use Jcsp\Queue\Annotation\Mapping\Pull;
use Jcsp\Queue\Result;
use Jcsp\WsCluster\Cluster;
use Jcsp\WsCluster\ClusterManager;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\Db\Exception\DbException;
use Swoft\Log\Helper\CLog;
use Swoft\Process\Process;
use Jcsp\Queue\Contract\UserProcess;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Timer;
use function foo\func;

/**
 * Class MonitorProcess
 *
 * @since 2.0
 *
 * @Bean()
 */
class RecvMessageProcess extends UserProcess
{
    /**
     * @var ClusterManager
     */
    private $clusterManager;

    public function init()
    {
        $this->clusterManager = BeanFactory::getBean(Cluster::MANAGER);
    }

    /**
     * @param Process $process
     * @Pull()
     */
    public function run(Process $process): void
    {
        $this->heartbeat();
        /** @var RedisState $redisState */
        $redisState = BeanFactory::getBean(Cluster::STATE);
        //add queue
        $this->queue = $redisState->getPrefix() . ':message:' . $redisState->getServerId();
        //waite
    }

    /**
     * 心跳检测 互相检测
     * @throws \Swoft\Exception\SwoftException
     */
    protected function heartbeat()
    {
        $timeout = $this->clusterManager->getHeartbeat();
        Timer::tick(1000 * $timeout, function() use ($timeout) {
            //更新时间
            $this->clusterManager->discover();
            //检测其他机器
            $otherServer = Arr::except($this->clusterManager->getServerIds(), $this->clusterManager->getServerId());
            foreach ($otherServer as $server => $time) {
                if($time < time() - 2 * $timeout) {
                    $this->clusterManager->shutdown($server);
                }
            }
        });
    }
    /**
     * customer
     * @param $message
     * @return string
     */
    public function receive($message): string
    {
        if (is_array($message) && count($message) === 2) {
            [$content, $fd] = $message;
            $server = server();
            if ($fd === null) {
                $server->sendToAll($content);
                CLog::debug('ws receive message by cluster message:%s all', $content);
            }
            if (is_array($fd)) {
                foreach ($fd as $id) {
                    $server->sendTo((int)$id, $content);
                    CLog::debug('ws receive message by cluster message:%s fd:%s', $content, $id);
                }
            }
        }
        return Result::ACK;
    }

    /**
     * when error callback
     * @param $message
     * @return string
     */
    public function fallback(\Throwable $throwable, int $retry): void
    {
        vdump('error', $throwable->getMessage(), $retry);
    }
}

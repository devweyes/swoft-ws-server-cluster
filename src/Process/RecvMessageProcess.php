<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Process;

use Jcsp\Queue\Annotation\Mapping\Pull;
use Jcsp\Queue\Result;
use Jcsp\WsCluster\Cluster;
use Jcsp\WsCluster\State\RedisState;
use Jcsp\WsCluster\StateInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\Db\Exception\DbException;
use Swoft\Log\Helper\CLog;
use Swoft\Process\Process;
use Jcsp\Queue\Contract\UserProcess;

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
     * @var StateInterface
     */
    private $state;

    public function init()
    {
        $this->state = BeanFactory::getBean(Cluster::STATE);
    }

    /**
     * @param Process $process
     * @Pull()
     */
    public function run(Process $process): void
    {
        /** @var RedisState $redisState */
        $redisState = BeanFactory::getBean(Cluster::STATE);
        //add queue
        $this->queue = $redisState->getPrefix() . ':message:' . $redisState->getServerId();
        //waite
    }

    /**
     * customer
     * @param $message
     * @return string
     */
    public function receive($message): string
    {
//        $message = $this->state->getSerializer()->unserialize($message);
        d('收消息', $message);
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
    public function fallback(\Throwable $throwable): void
    {
        d($throwable);
    }
}

<?php

namespace Jcsp\WsCluster\Aspect;

use Jcsp\WsCluster\ClusterManager;
use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\Annotation\Mapping\AfterReturning;
use Swoft\Aop\Annotation\Mapping\AfterThrowing;
use Swoft\Aop\Annotation\Mapping\Around;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\Before;
use Swoft\Aop\Annotation\Mapping\PointAnnotation;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Aop\Point\JoinPoint;
use Swoft\Aop\Point\ProceedingJoinPoint;
use Swoft\Bean\Annotation\Mapping\Inject;
use Jcsp\WsCluster\Annotation\Mapping\Cluster;

/**
 * Class ClusterAspect
 *
 * @since 2.0
 *
 * @Aspect(order=1)
 *
 * @PointAnnotation(include={Cluster::class})
 */
class ClusterAspect
{
    /**
     * @Inject()
     * @var ClusterManager
     */
    private $clusterManager;

    /**
     * @Around()
     *
     * @param ProceedingJoinPoint $proceedingJoinPoint
     *
     * @return mixed
     */
    public function around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // Before around
        $className = $proceedingJoinPoint->getClassName();
        $methodName = $proceedingJoinPoint->getMethod();
        $args = $proceedingJoinPoint->getArgs();
        //TODO 根据methodName调用相同操作，重用。
        $this->exec($args, $methodName);

        $result = $proceedingJoinPoint->proceed();
        // After around
        return $result;
    }

    protected function exec(array $args, string $methodName): void
    {
        if (in_array($methodName, ['onClose', 'onOpen'])) {
            $args = $this->getArgs($args, $methodName);
            $args[0]->{$args[1]}((int)$args[2]);
        }
    }

    protected function getArgs(array $args, string $methodName): array
    {
        if ($methodName === 'onClose') {
            return [$this->clusterManager,'logout', $args[1]];
        }
        if ($methodName === 'onOpen') {
            return [$this->clusterManager,'register', $args[2]];
        }


        if ($methodName === 'onMessage') {
            return [$args[1]->fd, $args[1]->data];
        }

        if ($methodName === 'checkHandshake') {
            return [1, $args[1]];
        }
        return [];
    }
}

<?php

namespace Jcsp\WsCluster\Aspect;

use Jcsp\WsCluster\Cluster;
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
use Swoft\WebSocket\Server\Annotation\Mapping\OnHandshake;

/**
 * Class OnHandshakeAspect
 *
 * @since 2.0
 *
 * @Aspect(order=1)
 *
 * @PointAnnotation(include={OnHandshake::class})
 */
class OnHandshakeAspect
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
        $args = $proceedingJoinPoint->getArgs();
        $middlewares = Cluster::getOnHandshakeMiddleware();

        reset($middlewares);
        while ($middleware = current($middlewares)) {
            $result = $middleware->before(...$args);
            [$status, $response] = $result;
            if($status === false) {
                return [$status, $response];
            }
            $args = [$args[0], $response];
            next($middlewares);
        }
        $result = $proceedingJoinPoint->proceed();
        // After around
        reset($middlewares);
        $middlewares = array_reverse($middlewares);
        while ($middleware = current($middlewares)) {
            $result = $middleware->after(...$args);
            [$status, $response] = $result;
            if($status === false) {
                return [$status, $response];
            }
            $args = [$args[0], $response];
            next($middlewares);
        }
        return $result;
    }
}

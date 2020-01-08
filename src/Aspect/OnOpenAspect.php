<?php

namespace Jcsp\WsCluster\Aspect;

use Jcsp\WsCluster\Cluster;
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
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;

/**
 * Class OnOpenAspect
 *
 * @since 2.0
 *
 * @Aspect(order=1)
 *
 * @PointAnnotation(include={OnOpen::class})
 */
class OnOpenAspect
{
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
        $middlewares = Cluster::getOnOpenMiddleware();

        reset($middlewares);
        while ($middleware = current($middlewares)) {
            $middleware->before(...$args);
            next($middlewares);
        }
        $result = $proceedingJoinPoint->proceed();
        // After around
        reset($middlewares);
        $middlewares = array_reverse($middlewares);
        while ($middleware = current($middlewares)) {
            $middleware->after(...$args);
            next($middlewares);
        }
        return $result;
    }
}

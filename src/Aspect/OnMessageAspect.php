<?php

namespace Jcsp\WsCluster\Aspect;

use Jcsp\WsCluster\Cluster;
use Jcsp\WsCluster\Event;
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
use Swoft\WebSocket\Server\Annotation\Mapping\OnMessage;
use Swoft\WebSocket\Server\Context\WsMessageContext;

/**
 * Class OnMessageAspect
 *
 * @since 2.0
 *
 * @Aspect(order=1)
 *
 * @PointAnnotation(include={OnMessage::class})
 */
class OnMessageAspect
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
        $className = $proceedingJoinPoint->getClassName();
        $methodName = $proceedingJoinPoint->getMethod();
        $args = $proceedingJoinPoint->getArgs();
        //收消息消息事件
        /** @var WsMessageContext $context */
        $context = context();
        if($context instanceof WsMessageContext) {
            Event::recvMessage(Cluster::getServerId(), $context->getFd(), $context->getMessage());
        }
        $result = $proceedingJoinPoint->proceed();
        // After around

        return $result;
    }
}

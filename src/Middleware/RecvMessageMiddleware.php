<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Middleware;

use Jcsp\WsCluster\ClusterManager;
use Jcsp\WsCluster\Event;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\WebSocket\Server\Contract\MessageHandlerInterface;
use Swoft\WebSocket\Server\Contract\MiddlewareInterface;
use Swoft\WebSocket\Server\Contract\RequestInterface;
use Swoft\WebSocket\Server\Contract\ResponseInterface;

/**
 * Class RecvMessageMiddleware
 *
 * @since 2.0
 *
 * @Bean()
 */
class RecvMessageMiddleware implements MiddlewareInterface
{
    /**
     * @Inject()
     * @var ClusterManager
     */
    private $clusterManager;

    /**
     * @param RequestInterface $request
     * @param MessageHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, MessageHandlerInterface $handler): ResponseInterface
    {
        Event::recvMessage($this->clusterManager->getServerId(), $request->getFd(), $request->getRawData());
        $response = $handler->handle($request);

        return $response;
    }
}

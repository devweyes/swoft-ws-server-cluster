<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Middleware;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\WebSocket\Server\Contract\MessageHandlerInterface;
use Swoft\WebSocket\Server\Contract\MiddlewareInterface;
use Swoft\WebSocket\Server\Contract\RequestInterface;
use Swoft\WebSocket\Server\Contract\ResponseInterface;

/**
 * Class DefaultMiddleware
 *
 * @since 2.0
 *
 * @Bean()
 */
class MessageMiddleware implements MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param MessageHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, MessageHandlerInterface $handler): ResponseInterface
    {
    	d($request);
        return $handler->handle($request);
    }
}

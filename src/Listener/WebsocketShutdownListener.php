<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Listener;

use Jcsp\WsCluster\Cluster;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Server\SwooleEvent;
use Swoft\Event\EventInterface;
use Swoft\WebSocket\Server\WebSocketServer;

/**
 * Class WebsocketShutdownListener
 *
 * @since 2.0
 *
 * @Listener(event=SwooleEvent::SHUTDOWN)
 */
class WebsocketShutdownListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @author kay
     */
    public function handle(EventInterface $event): void
    {
        if ($event->getTarget() instanceof WebSocketServer) {
            Cluster::shutdown();
        }
    }
}

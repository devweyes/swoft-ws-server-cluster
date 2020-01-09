<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Listener;

use Jcsp\WsCluster\Cluster;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Server\SwooleEvent;
use Swoft\WebSocket\Server\WebSocketServer;

/**
 * Class WebsocketStartListener
 *
 * @since 2.0
 *
 * @Listener(event=SwooleEvent::START)
 */
class WebsocketStartListener implements EventHandlerInterface
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
            Cluster::discover();
        }
    }
}

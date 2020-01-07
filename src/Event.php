<?php declare(strict_types=1);


namespace Jcsp\WsCluster;

use Swoft;

class Event
{
    public const RECV_MESSAGE = 'websocket:cluster:recv_message';

    public static function recvMessage(string $serverId, int $fd, string $message)
    {
        Swoft::trigger(self::RECV_MESSAGE, $serverId, $fd, $message);
    }
}

<?php declare(strict_types=1);


namespace Jcsp\WsCluster;

use Swoft;

class Event
{
    public const RECV_MESSAGE = 'websocket:cluster:recv_message';
    public const REGISTER = 'websocket:cluster:register';
    public const LOGOUT = 'websocket:cluster:lougout';
    public const DISCOVER = 'websocket:cluster:discover';
    public const SHUTDOWN = 'websocket:cluster:shutdown';

    /**
     * @param string $serverId
     * @param int $fd
     * @param string $message
     * @throws Swoft\Bean\Exception\ContainerException
     */
    public static function recvMessage(string $serverId, int $fd, string $message): void
    {
        Swoft::trigger(self::RECV_MESSAGE, $serverId, $fd, $message);
    }
    /**
     * @param string $serverId
     * @param int $fd
     * @param string $uid
     * @throws Swoft\Bean\Exception\ContainerException
     */
    public static function register(string $serverId, int $fd, string $uid): void
    {
        Swoft::trigger(self::REGISTER, $serverId, $fd);
    }
    /**
     * @param string $serverId
     * @param int $fd
     * @throws Swoft\Bean\Exception\ContainerException
     */
    public static function logout(string $serverId, int $fd): void
    {
        Swoft::trigger(self::LOGOUT, $serverId, $fd);
    }
    /**
     * @param string $serverId
     * @throws Swoft\Bean\Exception\ContainerException
     */
    public static function discover(string $serverId): void
    {
        Swoft::trigger(self::DISCOVER, $serverId);
    }
    /**
     * @param string $serverId
     * @throws Swoft\Bean\Exception\ContainerException
     */
    public static function shutdown(string $serverId): void
    {
        Swoft::trigger(self::SHUTDOWN, $serverId);
    }
}

<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

use Swoft\Bean\BeanFactory;

/**
 * Class Cluster
 * @package Jcsp\WsCluster
 * @method static bool register(int $fdid, string $uid = null)
 * @method static bool logout(int $fdid)
 * @method static bool transport(string $message, $uid = null)
 * @method static bool transportToUid(string $message, ...$uid)
 * @method static bool transportToAll(string $message)
 * @method static void shutdown()
 * @method static void discover()
 * @method static array getServerIds()
 * @method static string getServerId()
 * @method static array getOnOpenMiddleware()
 * @method static array getOnHandshakeMiddleware()
 */
class Cluster
{
    public const MANAGER = 'websocket.cluster.manager';
    public const STATE = 'websocket.cluster.state';
    public const SERIALIZER = 'websocket.cluster.serializer';

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     * @throws RedisException
     */
    public static function __callStatic(string $method, array $arguments)
    {
        $cacheManager = self::manager();
        return $cacheManager->{$method}(...$arguments);
    }
    /**
     * @return mixed|object|string
     */
    public static function manager()
    {
        return BeanFactory::getBean(self::MANAGER);
    }
}

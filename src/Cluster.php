<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

use Swoft\Bean\BeanFactory;

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

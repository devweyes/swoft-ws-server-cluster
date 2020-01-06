<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

use Jcsp\WsCluster\State\RedisState;
use Swoft\Console\Application;
use Swoft\Console\ConsoleDispatcher;
use Swoft\Console\Router\Router;
use Swoft\Helper\ComposerJSON;
use Swoft\Serialize\PhpSerializer;
use Swoft\SwoftComponent;
use function dirname;

/**
 * class AutoLoader
 *
 * @since 2.0
 */
final class AutoLoader extends SwoftComponent
{
    /**
     * @return bool
     */
    public function enable(): bool
    {
        return true;
    }

    /**
     * Get namespace and dirs
     *
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * Metadata information for the component
     *
     * @return array
     */
    public function metadata(): array
    {
        $jsonFile = dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * {@inheritDoc}
     */
    public function beans(): array
    {
        return [
            Cluster::MANAGER => [
                'class' => ClusterManager::class,
                'state' => bean(Cluster::STATE),
                'serverIdPrefix' => 'swoft_ws_server_cluster_'
            ],
            Cluster::STATE => [
                'class' => RedisState::class,
                'redis' => bean('redis.pool'),
                'uidHashMapPrefix' => 'swoft_ws_server_cluster:uid',
                'serverIdHashMapPrefix' => 'swoft_ws_server_cluster:server_',
                'messageQueuePrefix' => 'swoft_ws_server_cluster:message_',
                'serverIdsPrefix' => 'swoft_ws_server_cluster:serverids'
            ]
        ];
    }
}

<?php

namespace Jcsp\WsCluster\Middleware;

use Jcsp\WsCluster\Cluster;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Request;

/**
 * @Bean()
 * Class DefaultAuthMiddleware
 * @package Jcsp\WsCluster\Middleware
 */
class DefaultAuthMiddleware extends AbstractOpenMiddleware
{
    /**
     * @param Request $request
     * @param int $fd
     */
    public function before(Request $request, int $fd):void
    {
        $auth = $request->getHeaderLine('sec-websocket-protocol');
        if ($auth) {
            Cluster::register($fd, $this->decodeToken($auth));
        }
    }

    private function decodeToken(string $auth)
    {
        return $auth;
    }
}

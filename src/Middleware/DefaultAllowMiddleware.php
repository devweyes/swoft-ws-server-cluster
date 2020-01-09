<?php

namespace Jcsp\WsCluster\Middleware;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;

/**
 * @Bean()
 * Class DefaultAllowMiddleware
 * @package Jcsp\WsCluster\Middleware
 */
class DefaultAllowMiddleware extends AbstracHandshakeMiddleware
{
    /**
     * @param Request $request
     * @param int $fd
     */
    public function before(Request $request, Response $response): array
    {
        if (!$request->getHeaderLine('auth')) {
            //return [false, $response];
        }
        return [true, $response->withAttribute('allow', 'true')];
    }
    /**
     * @param Request $request
     * @param int $fd
     */
    public function after(Request $request, Response $response): array
    {
        return parent::after($request, $response);
    }
}

<?php

namespace Jcsp\WsCluster\Middleware;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;

abstract class AbstracHandshakeMiddleware
{
    /**
     * @param Request $request
     * @param int $fd
     */
    public function before(Request $request, Response $response): array
    {
        return [true, $response];
    }
    /**
     * @param Request $request
     * @param int $fd
     */
    public function after(Request $request, Response $response): array
    {
        return [true, $response];
    }
}

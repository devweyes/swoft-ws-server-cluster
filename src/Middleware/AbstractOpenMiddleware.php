<?php

namespace Jcsp\WsCluster\Middleware;

use Swoft\Http\Message\Request;

abstract class AbstractOpenMiddleware
{
    /**
     * @param Request $request
     * @param int $fd
     */
    public function before(Request $request, int $fd):void
    {

    }
    /**
     * @param Request $request
     * @param int $fd
     */
    public function after(Request $request, int $fd):void
    {

    }
}

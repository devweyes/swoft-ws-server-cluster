<?php

namespace Jcsp\WsCluster\State;

class LuaScripts
{
    /**
     * @return bool
     */
    public static function register()
    {
        return <<<'LUA'
-- add uid hash
redis.call('hset', KEYS[1], ARGV[1], ARGV[3])

-- add serverid hash
redis.call('hset', KEYS[2], ARGV[2], ARGV[1])

return true
LUA;
    }
    /**
     * @return bool
     */
    public static function logout()
    {
        return <<<'LUA'
local job = redis.call('hget', KEYS[2], ARGV[2])

if(not (job == nil)) then
    redis.call('hdel', KEYS[1], job)
    redis.call('hdel', KEYS[2], ARGV[1])
    return false
end

return true
LUA;
    }
}

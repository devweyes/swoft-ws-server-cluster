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
redis.call('hset', KEYS[2], ARGV[2], ARGV[4])

return true
LUA;
    }

    /**
     * @return bool
     */
    public static function logout()
    {
        return <<<'LUA'
local uidServer = redis.call('hget', KEYS[2], ARGV[1])

if(not (uidServer == nil)) then
    local uid = string.match(uidServer, '%w+|*')
    if(not (uid == nil)) then
        redis.call('hdel', KEYS[2], ARGV[1])
        redis.call('hdel', KEYS[1], uidServer)
        return true
    end
end

return false
LUA;
    }
}

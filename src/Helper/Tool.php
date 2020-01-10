<?php declare(strict_types=1);

namespace Jcsp\WsCluster\Helper;

use Exception;
use Swoft\Bean\BeanFactory;

class Tool
{
    /**
     * @param int $lenght
     * @return bool|string
     * @throws Exception
     */
    public static function uniqidReal($lenght = 13)
    {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes((int)(ceil($lenght / 2)));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception('no cryptographically secure random function available');
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    /**
     * @param string $name
     * @param $process
     * @param int $num
     * @return array
     */
    public static function moreProcess(string $name, $process, int $num = 1): array
    {
        $class = [];
        for ($i = 1; $i <= $num; $i++) {
            $class["$name:$i"] = $process;
        }

        return $class;
    }
}

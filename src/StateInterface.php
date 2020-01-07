<?php declare(strict_types=1);

namespace Jcsp\WsCluster;

interface StateInterface
{
    /**
     * register uid
     * @param int $fdid
     * @param string|null $uid
     */
    public function register(int $fdid, string $uid = null): bool;

    /**
     * logout
     * @param int $fdid
     */
    public function logout(int $fdid): bool;

    /**
     * transport message
     * @param string $message
     * @param null $targetUid
     * @param null $targetFdid
     * @param int|null $originUid
     * @param int|null $originFdid
     * @return bool
     */
    public function transport(
        string $message,
        $targetUid = null,
        $targetFdid = null,
        int $originUid = null,
        int $originFdid = null
    ): bool;
    /**
     * shutdown
     */
    public function shutdown(): void;
    /**
     * discover
     */
    public function discover(): void;
    /**
     * @return array
     */
    public function getServerIds(): array;
}

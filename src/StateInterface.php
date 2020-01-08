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
     * @param string $message
     * @param null $uid
     * @return bool
     */
    public function transport(string $message, $uid = null): bool;

    /**
     * @param string $message
     * @param mixed ...$uid
     * @return bool
     */
    public function transportToUid(string $message, ...$uid): bool;

    /**
     * @param string $message
     * @return bool
     */
    public function transportToAll(string $message): bool;

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

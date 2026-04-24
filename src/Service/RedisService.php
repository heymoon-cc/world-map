<?php

namespace App\Service;

use Redis;
use RedisException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class RedisService implements RedisServiceInterface
{
    private ?Redis $client = null;
    public function __construct(private readonly string $host,
                                private readonly int $port) {}

    /**
     * @throws RedisException
     */
    public function getClient(): Redis
    {
        if ($this->client) {
            return $this->client;
        }
        $client = new Redis();
        if ($client->connect($this->host, $this->port)) {
            return $this->client = $client;
        }
        throw new ServiceUnavailableHttpException();
    }
}

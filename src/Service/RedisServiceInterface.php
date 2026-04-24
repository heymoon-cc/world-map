<?php

namespace App\Service;

use Redis;
use RedisException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

interface RedisServiceInterface
{
    /**
     * @throws RedisException
     */
    public function getClient(): Redis;
}

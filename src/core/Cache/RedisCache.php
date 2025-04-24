<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace Core\Cache;

use BaksDev\Core\Cache\AppCacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

final class RedisCache implements AppCacheInterface
{
    private bool $forcibly = false;

    private string $type = RedisAdapter::class;

    private string $REDIS_HOST;
    private string $REDIS_PORT;
    private string $REDIS_PASSWORD;
    private string $HOST;

    public function __construct(
        #[Autowire(env: 'HOST')] string $HOST,
        #[Autowire(env: 'REDIS_HOST')] string $REDIS_HOST,
        #[Autowire(env: 'REDIS_PORT')] string $REDIS_PORT,
        #[Autowire(env: 'REDIS_PASSWORD')] string $REDIS_PASSWORD,
    )
    {
        $this->HOST = $HOST;
        $this->REDIS_HOST = $REDIS_HOST;
        $this->REDIS_PORT = $REDIS_PORT;
        $this->REDIS_PASSWORD = $REDIS_PASSWORD;
    }

    public function init(
        string $namespace = null,
        int $defaultLifetime = 86400,
        MarshallerInterface $marshaller = null
    ): CacheInterface
    {

        $namespace = $namespace ? $this->HOST.'.'.$namespace : $this->HOST;

        return new RedisAdapter(
            RedisAdapter::createConnection(
                sprintf(
                    'redis://%s@%s:%s',
                    $this->REDIS_PASSWORD,
                    $this->REDIS_HOST,
                    $this->REDIS_PORT
                )
            ),
            $namespace,
            $defaultLifetime,
            $marshaller
        );
    }

    public function getCacheType(): string
    {
        return $this->type;
    }
}

<?php

namespace App\Service;

use DateInterval;
use Psr\Cache\CacheItemPoolInterface;

class CacheService
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function putCache(string $name, $content,string $hours = '5'){
        $cacheItem = $this->cache->getItem($name);
        $cacheItem->set($content);
        $cacheItem->expiresAfter(DateInterval::createFromDateString($hours.' hour'));
        $this->cache->save($cacheItem);
    }

    public function getObject(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    public function getCache(string $name){
        $latest = $this->cache->getItem($name);
        return $latest->isHit() ? $latest->get() : false;
    }

    public function delete(string $name): bool
    {
        return $this->cache->deleteItem($name);
    }

    public function deleteMult(array $names): bool
    {
        return $this->cache->deleteItems($names);
    }
}
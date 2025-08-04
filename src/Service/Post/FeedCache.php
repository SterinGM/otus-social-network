<?php

namespace App\Service\Post;

use App\DTO\Post\Request\FeedRequest;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class FeedCache implements FeedInterface
{
    private const int CACHE_LIMIT = 1_000;
    private const string CACHE_PREFIX = 'feed.';

    private TagAwareCacheInterface $cache;
    private FeedInterface $feed;

    public function __construct(TagAwareCacheInterface $cache, FeedInterface $feed)
    {
        $this->cache = $cache;
        $this->feed = $feed;
    }

    public function get(FeedRequest $feedRequest): array
    {
        if ($feedRequest->offset >= self::CACHE_LIMIT) {
            return $this->feed->get($feedRequest);
        }

        $cacheKey = $this->getCacheKey($feedRequest);

        return $this->cache->get($cacheKey, function () use ($feedRequest) {
            return $this->feed->get($feedRequest);
        });
    }

    private function getCacheKey(FeedRequest $feedRequest): string
    {
        return self::CACHE_PREFIX . $feedRequest->userId . '_' . $feedRequest->limit . '_' . $feedRequest->offset;
    }
}
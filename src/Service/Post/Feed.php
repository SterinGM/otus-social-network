<?php

namespace App\Service\Post;

use App\DTO\Post\Request\FeedRequest;

class Feed implements FeedInterface
{
    public function get(FeedRequest $feedRequest): array
    {
        return [];
    }
}
<?php

namespace App\Service\Post;

use App\DTO\Post\Request\FeedRequest;
use App\Entity\Post;

interface FeedInterface
{
    /**
     * @param FeedRequest $feedRequest
     * @return Post[]
     */
    public function get(FeedRequest $feedRequest): array;
}
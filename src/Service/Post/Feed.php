<?php

namespace App\Service\Post;

use App\DTO\Post\Request\FeedRequest;
use App\Repository\Main\PostRepository;

class Feed implements FeedInterface
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function get(FeedRequest $feedRequest): array
    {
        return $this->postRepository->getLastFriendsPosts($feedRequest);
    }
}
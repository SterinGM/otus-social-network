<?php

namespace App\Controller\API\Post;

use App\DTO\Post\Request\FeedRequest;
use App\DTO\Post\Response\FeedResponse;
use App\Service\ApiJsonResponse;
use App\Service\Post\FeedInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class FeedController
{
    private FeedInterface $feed;

    public function __construct(FeedInterface $feed)
    {
        $this->feed = $feed;
    }

    #[Route('/post/feed', name: 'api_post_feed', methods: ['GET'])]
    public function __invoke(UserInterface $user, FeedRequest $feedRequest): JsonResponse
    {
        $feedRequest->userId = $user->getId();

        $result = $this->feed->get($feedRequest);

        return ApiJsonResponse::create(FeedResponse::createFromResult($result));
    }
}
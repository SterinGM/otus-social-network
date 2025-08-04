<?php

namespace App\Controller\API\Post;

use App\DTO\Post\Request\GetRequest;
use App\DTO\Post\Response\GetResponse;
use App\Service\ApiJsonResponse;
use App\Service\Post\PostProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetController
{
    private PostProviderInterface $postProvider;

    public function __construct(PostProviderInterface $postProvider)
    {
        $this->postProvider = $postProvider;
    }

    #[Route('/post/get/{id}', name: 'api_post_get', methods: ['GET'])]
    public function __invoke(GetRequest $getRequest): JsonResponse
    {
        $post = $this->postProvider->get($getRequest);

        return ApiJsonResponse::create(GetResponse::createFromPost($post));
    }
}
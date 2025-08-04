<?php

namespace App\Controller\API\Post;

use App\DTO\Post\Request\CreateRequest;
use App\DTO\Post\Response\CreateResponse;
use App\Service\ApiJsonResponse;
use App\Service\Post\PostProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CreateController
{
    private PostProviderInterface $postProvider;

    public function __construct(PostProviderInterface $postProvider)
    {
        $this->postProvider = $postProvider;
    }

    #[Route('/post/create', name: 'api_post_create', methods: ['POST'])]
    public function __invoke(UserInterface $user, CreateRequest $createRequest): JsonResponse
    {
        $createRequest->authorId = $user->getId();
        $post = $this->postProvider->create($createRequest);

        return ApiJsonResponse::create(CreateResponse::createFromPost($post)->postId);
    }
}
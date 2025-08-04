<?php

namespace App\Controller\API\Post;

use App\DTO\Post\Request\UpdateRequest;
use App\Service\Post\PostProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateController
{
    private PostProviderInterface $postProvider;

    public function __construct(PostProviderInterface $postProvider)
    {
        $this->postProvider = $postProvider;
    }

    #[Route('/post/update', name: 'api_post_update', methods: ['PUT'])]
    public function __invoke(UserInterface $user, UpdateRequest $updateRequest): JsonResponse
    {
        $updateRequest->authorId = $user->getId();
        $this->postProvider->update($updateRequest);

        return new JsonResponse();
    }
}
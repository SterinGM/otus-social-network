<?php

namespace App\Controller\API\Post;

use App\DTO\Post\Request\DeleteRequest;
use App\Service\ApiJsonResponse;
use App\Service\Post\PostProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DeleteController
{
    private PostProviderInterface $postProvider;

    public function __construct(PostProviderInterface $postProvider)
    {
        $this->postProvider = $postProvider;
    }

    #[Route('/post/delete/{id}', name: 'api_post_delete', methods: ['PUT'])]
    public function __invoke(UserInterface $user, DeleteRequest $deleteRequest): JsonResponse
    {
        $deleteRequest->authorId = $user->getId();

        $this->postProvider->delete($deleteRequest);

        return ApiJsonResponse::create();
    }
}
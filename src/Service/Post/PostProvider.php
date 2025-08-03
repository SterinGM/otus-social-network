<?php

namespace App\Service\Post;

use App\DTO\Post\Request\CreateRequest;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Component\Uid\Uuid;

class PostProvider implements PostProviderInterface
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository) {
        $this->postRepository = $postRepository;
    }

    public function create(CreateRequest $createRequest): Post
    {
        $post = $this->getPostFromRequest($createRequest);

        $this->postRepository->create($post);

        return $post;
    }

    private function getPostFromRequest(CreateRequest $createRequest): Post
    {
        $author = new User();
        $author->setId($createRequest->authorId);

        return new Post()
            ->setId(Uuid::v7()->toRfc4122())
            ->setAuthor($author)
            ->setText($createRequest->text);
    }
}
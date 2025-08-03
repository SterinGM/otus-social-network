<?php

namespace App\Service\Post;

use App\DTO\Post\Request\CreateRequest;
use App\DTO\Post\Request\UpdateRequest;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Service\Exception\PostNotFoundException;
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

    public function update(UpdateRequest $updateRequest): Post
    {
        $post = $this->postRepository->getById($updateRequest->id);

        if ($post === null) {
            throw new PostNotFoundException($updateRequest->id);
        }

        if ($post->getAuthor()->getId() !== $updateRequest->authorId) {
            throw new PostNotFoundException($updateRequest->id);
        }

        $post->setText($updateRequest->text);

        $this->postRepository->update($post);

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
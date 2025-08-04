<?php

namespace App\Service\Post;

use App\DTO\Post\Request\CreateRequest;
use App\DTO\Post\Request\DeleteRequest;
use App\DTO\Post\Request\GetRequest;
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
        $post = $this->getPost($updateRequest->id, $updateRequest->authorId);

        $post->setText($updateRequest->text);

        $this->postRepository->update($post);

        return $post;
    }

    public function delete(DeleteRequest $deleteRequest): void
    {
        $post = $this->getPost($deleteRequest->id, $deleteRequest->authorId);

        $this->postRepository->delete($post);
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

    public function get(GetRequest $getRequest): Post
    {
        return $this->getPost($getRequest->id);
    }

    protected function getPost(string $id, ?string $authorId = null): Post
    {
        $post = $this->postRepository->getById($id);

        if ($post === null) {
            throw new PostNotFoundException($id);
        }

        if ($authorId && $post->getAuthor()->getId() !== $authorId) {
            throw new PostNotFoundException($id);
        }

        return $post;
    }
}
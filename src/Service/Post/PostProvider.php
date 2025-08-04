<?php

namespace App\Service\Post;

use App\DTO\Post\Request\CreateRequest;
use App\DTO\Post\Request\DeleteRequest;
use App\DTO\Post\Request\GetRequest;
use App\DTO\Post\Request\UpdateRequest;
use App\Entity\Post;
use App\Entity\User;
use App\Event\Post\PostCreatedEvent;
use App\Event\Post\PostDeletedEvent;
use App\Event\Post\PostUpdatedEvent;
use App\Repository\PostRepository;
use App\Service\Exception\PostNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class PostProvider implements PostProviderInterface
{
    private PostRepository $postRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(PostRepository $postRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->postRepository = $postRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(CreateRequest $createRequest): Post
    {
        $post = $this->getPostFromRequest($createRequest);

        $this->postRepository->create($post);

        $event = new PostCreatedEvent($post);
        $this->eventDispatcher->dispatch($event);

        return $post;
    }

    public function update(UpdateRequest $updateRequest): Post
    {
        $post = $this->getPost($updateRequest->id, $updateRequest->authorId);

        $post->setText($updateRequest->text);

        $this->postRepository->update($post);

        $event = new PostUpdatedEvent($post);
        $this->eventDispatcher->dispatch($event);

        return $post;
    }

    public function delete(DeleteRequest $deleteRequest): void
    {
        $post = $this->getPost($deleteRequest->id, $deleteRequest->authorId);

        $this->postRepository->delete($post);

        $event = new PostDeletedEvent($post);
        $this->eventDispatcher->dispatch($event);
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
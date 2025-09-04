<?php

namespace App\Message\Post;

use DateTimeImmutable;

class PostCreatedMessage
{
    public readonly string $postId;
    public readonly string $authorId;
    /** @var string[] */
    public readonly array $friendIds;
    public readonly DateTimeImmutable $createdAt;

    /**
     * @param string[] $friendIds
     */
    public function __construct(string $postId, string $authorId, array $friendIds, DateTimeImmutable $createdAt)
    {
        $this->postId = $postId;
        $this->authorId = $authorId;
        $this->friendIds = $friendIds;
        $this->createdAt = $createdAt;
    }

    public function getRoutingKey(): string
    {
        return sprintf('post.created.%d', $this->authorId);
    }
}
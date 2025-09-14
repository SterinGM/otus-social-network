<?php

namespace App\DTO\Post\Response;

use App\Entity\Main\Post;

class CreateResponse
{
    public string $postId;

    private function __construct(string $postId)
    {
        $this->postId = $postId;
    }

    public static function createFromPost(Post $post): self
    {
        return new self(
            $post->getId(),
        );
    }
}
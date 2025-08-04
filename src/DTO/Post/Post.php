<?php

namespace App\DTO\Post;

use App\Entity\Post as PostDoctrine;

class Post
{
    public string $id;
    public string $text;
    public string $authorId;

    private function __construct(string $id, string $text, string $authorId)
    {
        $this->id = $id;
        $this->text = $text;
        $this->authorId = $authorId;
    }

    public static function createFromPost(PostDoctrine $post): self
    {
        return new self(
            $post->getId(),
            $post->getText(),
            $post->getAuthor()->getId(),
        );
    }
}
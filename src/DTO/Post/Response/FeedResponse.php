<?php

namespace App\DTO\Post\Response;

use App\DTO\Post\Post;
use App\Entity\Main\Post as PostDoctrine;

class FeedResponse
{
    /**
     * @var Post[]
     */
    public array $posts;

    /**
     * @param Post[] $posts
     */
    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @param PostDoctrine[] $doctrinePosts
     * @return self
     */
    public static function createFromResult(array $doctrinePosts): self
    {
        $posts = [];

        foreach ($doctrinePosts as $doctrinePost) {
            $posts[] = Post::createFromPost($doctrinePost);
        }

        return new self($posts);
    }
}
<?php

namespace App\Event\Post;

use App\Entity\Main\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostDeletedEvent extends Event
{
    public readonly Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
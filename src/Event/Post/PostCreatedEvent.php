<?php

namespace App\Event\Post;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostCreatedEvent extends Event
{
    public readonly Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
<?php

namespace App\Service\Post;

use App\DTO\Post\Request\CreateRequest;
use App\Entity\Post;

interface PostProviderInterface
{
    public function create(CreateRequest $createRequest): Post;
}
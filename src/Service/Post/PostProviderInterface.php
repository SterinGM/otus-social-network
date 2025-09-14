<?php

namespace App\Service\Post;

use App\DTO\Post\Request\CreateRequest;
use App\DTO\Post\Request\DeleteRequest;
use App\DTO\Post\Request\GetRequest;
use App\DTO\Post\Request\UpdateRequest;
use App\Entity\Main\Post;

interface PostProviderInterface
{
    public function create(CreateRequest $createRequest): Post;

    public function update(UpdateRequest $updateRequest): Post;

    public function delete(DeleteRequest $deleteRequest): void;

    public function get(GetRequest $getRequest): Post;
}
<?php

namespace App\Service\Search;

use App\DTO\User\Request\SearchRequest;
use App\Entity\Main\User;

interface SearchInterface
{
    /**
     * @return User[]
     */
    public function searchUsers(SearchRequest $searchRequest): array;
}
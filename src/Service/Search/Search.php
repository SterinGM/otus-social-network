<?php

namespace App\Service\Search;

use App\DTO\User\Request\SearchRequest;
use App\Repository\Main\UserRepository;

class Search implements SearchInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function searchUsers(SearchRequest $searchRequest): array
    {
        return $this->userRepository->search(
            $searchRequest->firstName,
            $searchRequest->lastName
        );
    }
}
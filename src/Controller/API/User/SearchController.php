<?php

namespace App\Controller\API\User;

use App\DTO\User\Request\SearchRequest;
use App\DTO\User\Response\SearchResponse;
use App\Service\ApiJsonResponse;
use App\Service\Search\SearchInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SearchController
{
    private SearchInterface $search;

    public function __construct(SearchInterface $search)
    {
        $this->search = $search;
    }

    #[Route('/user/search', name: 'api_user_search', methods: ['GET'])]
    public function __invoke(SearchRequest $searchRequest): JsonResponse
    {
        $result = $this->search->searchUsers($searchRequest);

        return ApiJsonResponse::create(SearchResponse::createFromResult($result)->users);
    }
}
<?php

namespace App\Controller\API\User;

use App\DTO\User\Request\SearchRequest;
use App\DTO\User\Response\SearchResponse;
use App\Service\ApiJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SearchController
{
    #[Route('/user/search', name: 'api_user_search', methods: ['GET'])]
    public function __invoke(SearchRequest $searchRequest): JsonResponse
    {
        return ApiJsonResponse::create(SearchResponse::createFromResult([]));
    }
}
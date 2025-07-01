<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiJsonResponse extends JsonResponse
{
    public static function create(mixed $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        $response = new JsonResponse($data, $status, $headers, $json);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $response;
    }
}
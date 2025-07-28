<?php

namespace App\Service\ApiLogger;

use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\StopwatchPeriod;

readonly class ApiData
{
    public string $id;
    public string $userId;
    public string $uri;
    public string $request;
    public string $response;
    public int $time;
    public int $memory;
    public DateTimeInterface $createdDate;

    public function __construct(
        string $id,
        string $userId,
        string $uri,
        string $request,
        string $response,
        int $time,
        int $memory,
        DateTimeInterface $createdDate,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->uri = $uri;
        $this->request = $request;
        $this->response = $response;
        $this->time = $time;
        $this->memory = $memory;
        $this->createdDate = $createdDate;
    }

    public static function create(
        StopwatchPeriod $stopwatchPeriod,
        Request $request,
        Response $response,
        ?string $userId = null,
    ): self
    {
        $requestData = json_decode($request->getContent(), true);
        $requestJson = $requestData ? json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        $responseData = $response->getContent() !== false ? json_decode($response->getContent(), true) : null;
        $responseJson = $responseData ? json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        $requestId = $request->attributes->get('api_request_id', '');
        $requestUri = $request->getPathInfo() . ($request->getQueryString() !== '' ? '?' . urldecode($request->getQueryString()) : '');

        return new self(
            $requestId,
            $userId ?? '',
            $requestUri,
            (string)$requestJson,
            (string)$responseJson,
            (int)$stopwatchPeriod->getEndTime(),
            $stopwatchPeriod->getMemory(),
            new DateTime(),
        );
    }
}
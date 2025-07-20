<?php

namespace App\Service\ApiLogger;

use App\Entity\ApiLog;
use App\Repository\ApiLogRepository;
use DateTimeImmutable;

class DBApiLogger implements ApiLoggerInterface
{
    private ApiLogRepository $apiLogRepository;

    public function __construct(ApiLogRepository $apiLogRepository)
    {
        $this->apiLogRepository = $apiLogRepository;
    }

    public function log(ApiData $data): void
    {
        $log = $this->mapLog($data);

        $this->apiLogRepository->save($log, true);
    }

    private function mapLog(ApiData $data): ApiLog
    {
        $log = new ApiLog();
        $log->setId($data->id);
        $log->setUserId($data->userId);
        $log->setUri($data->uri);
        $log->setRequest($data->request);
        $log->setResponse($data->response);
        $log->setTime($data->time);
        $log->setMemory($data->memory);
        $log->setCreatedAt(DateTimeImmutable::createFromInterface($data->createdDate));

        return $log;
    }
}
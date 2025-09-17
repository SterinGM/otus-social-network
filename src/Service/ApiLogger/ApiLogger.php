<?php

namespace App\Service\ApiLogger;

use App\Entity\Main\ApiLog;
use App\Repository\Main\ApiLogRepository;
use App\Service\Config\ConfigInterface;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class ApiLogger implements ApiLoggerInterface
{
    private ApiLogRepository $apiLogRepository;
    private ConfigInterface $config;

    public function __construct(ApiLogRepository $apiLogRepository, ConfigInterface $config)
    {
        $this->apiLogRepository = $apiLogRepository;
        $this->config = $config;
    }

    public function log(ApiData $data): void
    {
        if (!$this->config->isEnabled(ConfigInterface::API_LOG_LOGIC)) {
            return;
        }

        $log = $this->mapLog($data);

        $this->apiLogRepository->save($log);
    }

    public function generateId(Request $request): void
    {
        $id = Uuid::v7()->toRfc4122();

        $request->attributes->set('api_request_id', $id);
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
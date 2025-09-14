<?php

namespace App\EventListener;

use App\Entity\Main\User;
use App\Service\ApiLogger\ApiData;
use App\Service\ApiLogger\ApiLoggerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ApiSubscriber implements EventSubscriberInterface
{
    private ApiLoggerInterface $requestLogger;
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private Stopwatch $stopwatch;

    public function __construct(
        ApiLoggerInterface $requestLogger,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
    ) {
        $this->requestLogger = $requestLogger;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->stopwatch = new Stopwatch();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest'],
            ResponseEvent::class => ['onResponse'],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $this->stopwatch->start('api');

        $request = $event->getRequest();

        if (!$this->isApiRequest($request)) {
            return;
        }

        $this->requestLogger->generateId($request);
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$this->stopwatch->isStarted('api')) {
            return;
        }

        $stopwatchEvent = $this->stopwatch->stop('api');

        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isApiRequest($request)) {
            return;
        }

        /** @var User|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        try {
            $data = ApiData::create($stopwatchEvent->getPeriods()[0], $request, $event->getResponse(), $user?->getId());

            $this->requestLogger->log($data);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }
    }

    private function isApiRequest(Request $request): bool
    {
        /** @var string $route */
        $route = $request->get('_route');

        return $route && str_starts_with($route, 'api_');
    }
}
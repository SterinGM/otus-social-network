<?php

namespace App\EventListener;

use App\DTO\Error;
use App\Service\ApiJsonResponse;
use App\Service\ErrorSystem\ErrorCode;
use App\Service\ExceptionInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    private LoggerInterface $logger;
    private TranslatorInterface $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @throws Exception
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        /** @var Exception $exception */
        $exception = $event->getThrowable();

        if ($this->isApiRequest($event->getRequest()) === false) {
            return;
        }

        if ($exception instanceof ExceptionInterface) {
            $this->logger->warning($exception->getMessage(), $exception->getTrace());
            $message = $exception->getTranslationMessage()?->trans($this->translator) ?? $exception->getMessage();
            $response = $this->getJsonResponse($event->getRequest(), $message, $exception->getErrorCode(), Response::HTTP_NOT_FOUND);
            $event->setResponse($response);

            return;
        }

        if ($exception->getPrevious() instanceof InsufficientAuthenticationException) {
            $this->logger->warning($exception->getMessage(), $exception->getTrace());
            $message = new TranslatableMessage(ErrorCode::INVALID_CREDENTIALS->translateCode(), [], 'errors')->trans($this->translator);
            $response = $this->getJsonResponse($event->getRequest(), $message, ErrorCode::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED);
            $event->setResponse($response);

            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $this->logger->warning($exception->getMessage(), $exception->getTrace());
            $message = new TranslatableMessage(ErrorCode::INVALID_PARAMS->translateCode(), [], 'errors')->trans($this->translator);
            $response = $this->getJsonResponse($event->getRequest(), $message, ErrorCode::INVALID_PARAMS);
            $event->setResponse($response);

            return;
        }

        $this->logger->error($exception->getMessage(), $exception->getTrace());
        $message = new TranslatableMessage(ErrorCode::SERVER_ERROR->translateCode(), [], 'errors')->trans($this->translator);
        $response = $this->getJsonResponse($event->getRequest(), $message, ErrorCode::SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }

    private function getJsonResponse(Request $request, string $message, ErrorCode $errorCode, int $statusCode = Response::HTTP_BAD_REQUEST): Response
    {
        $errors = (array) $request->attributes->get('errors', []);
        $requestId = $request->attributes->get('api_request_id', '');
        $data = Error::create($errorCode, $message, $errors, $requestId);

        return ApiJsonResponse::create($data, $statusCode);
    }

    private function isApiRequest(Request $request): bool
    {
        /** @var string $route */
        $route = $request->get('_route', '');

        return $route && str_starts_with($route, 'api_');
    }
}

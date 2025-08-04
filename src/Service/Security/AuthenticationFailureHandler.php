<?php

namespace App\Service\Security;

use App\DTO\Error;
use App\Service\ApiJsonResponse;
use App\Service\ErrorSystem\ErrorCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $errorCode = ErrorCode::INVALID_CREDENTIALS;
        $errors = (array) $request->attributes->get('errors', []);
        $requestId = $request->attributes->get('api_request_id', '');
        $message = new TranslatableMessage($errorCode->translateCode(), [], 'errors')->trans($this->translator);
        $data = Error::create($errorCode, $message, $errors, $requestId);

        return ApiJsonResponse::create($data, Response::HTTP_UNAUTHORIZED);
    }
}
<?php

namespace App\Service\Exception;

use App\Service\ErrorSystem\ErrorCode;
use App\Service\ErrorSystem\TranslationMessageTrait;
use App\Service\ExceptionInterface;
use RuntimeException;
use Symfony\Component\Translation\TranslatableMessage;

class InvalidCredentialsException extends RuntimeException implements ExceptionInterface
{
    use TranslationMessageTrait;

    public function __construct()
    {
        $message = new TranslatableMessage($this->getErrorCode()->translateCode(), [], 'errors');

        $this->setTranslationMessage($message);

        parent::__construct($this->getMessageData());
    }

	public function getErrorCode(): ErrorCode
	{
		return ErrorCode::INVALID_CREDENTIALS;
	}
}
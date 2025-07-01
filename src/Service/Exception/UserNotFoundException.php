<?php

namespace App\Service\Exception;

use App\Service\ErrorSystem\ErrorCode;
use App\Service\ErrorSystem\TranslationMessageTrait;
use App\Service\ExceptionInterface;
use RuntimeException;
use Symfony\Component\Translation\TranslatableMessage;

class UserNotFoundException extends RuntimeException implements ExceptionInterface
{
    use TranslationMessageTrait;

    public function __construct(string $id)
    {
        $message = new TranslatableMessage($this->getErrorCode()->translateCode(), ['%id%' => $id], 'errors');

        $this->setTranslationMessage($message);

        parent::__construct($this->getMessageData());
    }

	public function getErrorCode(): ErrorCode
	{
		return ErrorCode::USER_NOT_FOUND;
	}
}
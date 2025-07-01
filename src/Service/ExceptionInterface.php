<?php

namespace App\Service;

use App\Service\ErrorSystem\ErrorCode;
use Symfony\Component\Translation\TranslatableMessage;

interface ExceptionInterface
{
	public function getErrorCode(): ErrorCode;

    public function setTranslationMessage(TranslatableMessage $message): void;

    public function getTranslationMessage(): ?TranslatableMessage;
}
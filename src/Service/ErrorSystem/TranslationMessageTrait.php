<?php

namespace App\Service\ErrorSystem;

use Symfony\Component\Translation\TranslatableMessage;

trait TranslationMessageTrait
{
    private ?TranslatableMessage $translatableMessage = null;

    public function setTranslationMessage(TranslatableMessage $message): void
    {
        $this->translatableMessage = $message;
    }

    public function getTranslationMessage(): ?TranslatableMessage
    {
        return $this->translatableMessage;
    }

    public function getMessageData(): string
    {
        if ($this->translatableMessage === null) {
            return '';
        }

        return $this->translatableMessage->getMessage() . ' TranslationMessageTrait.php' . json_encode($this->translatableMessage->getParameters());
    }
}
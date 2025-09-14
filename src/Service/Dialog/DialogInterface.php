<?php

namespace App\Service\Dialog;

use App\DTO\Dialog\Request\ListRequest;
use App\DTO\Dialog\Request\SendRequest;
use App\Entity\Dialog\Message;

interface DialogInterface
{
    public function sendMessage(SendRequest $sendRequest): void;

    /**
     * @return Message[]
     */
    public function getMessages(ListRequest $listRequest): array;
}
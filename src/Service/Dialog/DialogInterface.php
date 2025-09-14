<?php

namespace App\Service\Dialog;

use App\DTO\Dialog\Request\SendRequest;

interface DialogInterface
{
    public function sendMessage(SendRequest $sendRequest): void;
}
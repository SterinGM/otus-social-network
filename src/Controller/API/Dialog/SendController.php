<?php

namespace App\Controller\API\Dialog;

use App\DTO\Dialog\Request\SendRequest;
use App\Service\ApiJsonResponse;
use App\Service\Dialog\DialogInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SendController
{
    private DialogInterface $dialog;

    public function __construct(DialogInterface $dialog)
    {
        $this->dialog = $dialog;
    }

    #[Route('/dialog/{user_id}/send', name: 'api_dialog_send', methods: ['POST'])]
    public function __invoke(UserInterface $user, SendRequest $sendRequest): JsonResponse
    {
        $sendRequest->fromUserId = $user->getId();

        $this->dialog->sendMessage($sendRequest);

        return ApiJsonResponse::create();
    }
}
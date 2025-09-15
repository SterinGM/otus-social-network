<?php

namespace App\Controller\API\Dialog;

use App\DTO\Dialog\Request\ListRequest;
use App\DTO\Dialog\Response\ListResponse;
use App\Service\ApiJsonResponse;
use App\Service\Dialog\DialogInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ListController
{
    private DialogInterface $dialog;

    public function __construct(DialogInterface $dialog)
    {
        $this->dialog = $dialog;
    }

    #[Route('/dialog/{chat_id}/list', name: 'api_dialog_list', methods: ['GET'])]
    public function __invoke(UserInterface $user, ListRequest $listRequest): JsonResponse
    {
        $chat = $this->dialog->getChatById($listRequest->chatId);
        $messages = $this->dialog->getMessages($chat);

        return ApiJsonResponse::create(ListResponse::createFromResult($chat, $messages));
    }
}
<?php

namespace App\Controller\API\Dialog;

use App\DTO\Dialog\Request\NewRequest;
use App\DTO\Dialog\Response\NewResponse;
use App\Service\ApiJsonResponse;
use App\Service\Dialog\DialogInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class NewController
{
    private DialogInterface $dialog;

    public function __construct(DialogInterface $dialog)
    {
        $this->dialog = $dialog;
    }

    #[Route('/dialog/new', name: 'api_dialog_new', methods: ['POST'])]
    public function __invoke(UserInterface $user, NewRequest $newRequest): JsonResponse
    {
        $userIds = $newRequest->users;
        $userIds[] = $user->getUserIdentifier();
        $chat = $this->dialog->createChat($userIds);

        return ApiJsonResponse::create(NewResponse::createFromChat($chat));
    }
}
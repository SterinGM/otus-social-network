<?php

namespace App\Controller\API\User;

use App\DTO\User\Response\GetResponse;
use App\Service\ApiJsonResponse;
use App\Service\User\Profile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetController
{
    private Profile $profile;
    private ValidatorInterface $validator;

    public function __construct(Profile $profile, ValidatorInterface $validator)
    {
        $this->profile = $profile;
        $this->validator = $validator;
    }

    #[Route('/user/get/{id}', name: 'api_user_get', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        $this->validate($id);

        $user = $this->profile->getProfile($id);

        return ApiJsonResponse::create(GetResponse::createFromUser($user));
    }

    private function validate(string $id): void
    {
        $errors = $this->validator->validate($id, new Assert\Uuid());

        if ($errors->count()) {
            throw new BadRequestHttpException();
        }
    }
}
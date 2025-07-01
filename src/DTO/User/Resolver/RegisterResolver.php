<?php

namespace App\DTO\User\Resolver;

use App\DTO\User\Request\RegisterRequest;
use App\Service\ErrorSystem\Errors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterResolver implements ValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (RegisterRequest::class !== $argument->getType()) {
            return;
        }

        $data = $request->getPayload()->all();
        $errors = $this->validate($data);

        if (count($errors)) {
            $request->attributes->set('errors', $errors);

            throw new BadRequestHttpException();
        }

        yield RegisterRequest::createFromArray($data);
    }

    private function validate(array $data): array
    {
        $constraint = new Assert\Collection(fields: [
            RegisterRequest::FIELD_FIRST_NAME => new Assert\Length(min: 3, max: 50),
            RegisterRequest::FIELD_SECOND_NAME => new Assert\Length(min: 3, max: 50),
            RegisterRequest::FIELD_BIRTH_DATE => new Assert\Date(),
            RegisterRequest::FIELD_BIOGRAPHY => new Assert\Length(min: 3),
            RegisterRequest::FIELD_CITY => new Assert\Length(min: 3),
            RegisterRequest::FIELD_PASSWORD => new Assert\Length(min: 3),
        ]);

        $violations = $this->validator->validate($data, $constraint);

        return Errors::toArray($violations);
    }
}
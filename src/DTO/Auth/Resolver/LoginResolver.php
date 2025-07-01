<?php

namespace App\DTO\Auth\Resolver;

use App\DTO\Auth\Request\LoginRequest;
use App\Service\ErrorSystem\Errors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoginResolver implements ValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (LoginRequest::class !== $argument->getType()) {
            return;
        }

        $data = $request->getPayload()->all();
        $errors = $this->validate($data);

        if (count($errors)) {
            $request->attributes->set('errors', $errors);

            throw new BadRequestHttpException();
        }

        yield LoginRequest::createFromArray($data);
    }

    private function validate(array $data): array
    {
        $constraint = new Assert\Collection(fields: [
            LoginRequest::FIELD_ID => new Assert\Uuid(),
            LoginRequest::FIELD_PASSWORD => new Assert\Length(min: 3),
        ]);

        $violations = $this->validator->validate($data, $constraint);

        return Errors::toArray($violations);
    }
}
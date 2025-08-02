<?php

namespace App\DTO\Friend\Resolver;

use App\DTO\Friend\Request\SetRequest;
use App\Service\ErrorSystem\Errors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SetResolver implements ValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (SetRequest::class !== $argument->getType()) {
            return;
        }

        $data = $request->attributes->get('_route_params');
        $errors = $this->validate($data);

        if (count($errors)) {
            $request->attributes->set('errors', $errors);

            throw new BadRequestHttpException();
        }

        yield SetRequest::createFromArray($data);
    }

    private function validate(array $data): array
    {
        $constraint = new Assert\Collection(fields: [
            SetRequest::FIELD_USER_ID => new Assert\Uuid,
        ]);

        $violations = $this->validator->validate($data, $constraint);

        return Errors::toArray($violations);
    }
}
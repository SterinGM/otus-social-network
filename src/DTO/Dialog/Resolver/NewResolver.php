<?php

namespace App\DTO\Dialog\Resolver;

use App\DTO\Dialog\Request\NewRequest;
use App\Service\ErrorSystem\Errors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewResolver implements ValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (NewRequest::class !== $argument->getType()) {
            return;
        }

        $data = $request->attributes->get('_route_params') + $request->getPayload()->all();
        $errors = $this->validate($data);

        if (count($errors)) {
            $request->attributes->set('errors', $errors);

            throw new BadRequestHttpException();
        }

        yield NewRequest::createFromArray($data);
    }

    private function validate(array $data): array
    {
        $constraint = new Assert\Collection(fields: [
            NewRequest::FIELD_USERS => new Assert\All([
                'constraints' => new Assert\Uuid(),
            ]),
        ]);

        $violations = $this->validator->validate($data, $constraint);

        return Errors::toArray($violations);
    }
}
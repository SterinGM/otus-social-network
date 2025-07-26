<?php

namespace App\DTO\User\Resolver;

use App\DTO\User\Request\SearchRequest;
use App\Service\ErrorSystem\Errors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SearchResolver implements ValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (SearchRequest::class !== $argument->getType()) {
            return;
        }

        $data = $request->query->all();
        $errors = $this->validate($data);

        if (count($errors)) {
            $request->attributes->set('errors', $errors);

            throw new BadRequestHttpException();
        }

        yield SearchRequest::createFromArray($data);
    }

    private function validate(array $data): array
    {
        $constraint = new Assert\Collection(fields: [
            SearchRequest::FIELD_FIRST_NAME => new Assert\Length(min: 1, max: 50),
            SearchRequest::FIELD_LAST_NAME => new Assert\Length(min: 1, max: 50),
        ]);

        $violations = $this->validator->validate($data, $constraint);

        return Errors::toArray($violations);
    }
}
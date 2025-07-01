<?php

namespace App\Service\ErrorSystem;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Errors
{
    public static function toArray(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        if ($violations->count()) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $field = substr($violation->getPropertyPath(), 1, -1);
                $errors[$field] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
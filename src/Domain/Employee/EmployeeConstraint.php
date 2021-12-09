<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EmployeeConstraint
{
    public function __construct(array $data)
    {
        $errors = [];

        foreach ($data as $key => $value) {
            $violations = $this->getValidator()->validate($value, $this->{$key}());

            if (count($violations)) {
                $errors[] = (string)$violations;
            }
        }

        return $errors;
    }

    private function getValidator(): ValidatorInterface
    {
        return Validation::createValidator();
    }

    private function id(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function documentTypeId(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 3,
            ]),
        ];
    }

    private function documentNumber(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function firstName(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 80,
            ]),
        ];
    }

    private function secondName(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 80,
            ]),
        ];
    }

    private function firstSurname(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 80,
            ]),
        ];
    }

    private function secondSurname(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 80,
            ]),
        ];
    }

    private function type(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function subType(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function paymentMethod(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 5,
            ]),
        ];
    }

    private function accountType(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 3,
            ]),
        ];
    }

    private function accountNumber(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 80,
            ]),
        ];
    }

    private function isActive(): array
    {
        return [
            new Assert\Type([
                'type' => 'bool',
            ]),
        ];
    }

    private function createdBy(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function updatedBy(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }
}

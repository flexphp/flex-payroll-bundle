<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PaysheetConstraint
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

    private function type(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 2,
            ]),
        ];
    }

    private function customerId(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function vehicleId(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function kilometers(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function kilometersToChange(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function discount(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function subtotal(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function taxes(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function total(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function notes(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function totalPaid(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function paidAt(): array
    {
        return [
            new Assert\DateTime(),
        ];
    }

    private function statusId(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 2,
            ]),
        ];
    }

    private function billNotes(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function expiratedAt(): array
    {
        return [
            new Assert\DateTime(),
        ];
    }

    private function worker(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
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

<?php declare(strict_types=1);

namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PayrollConstraint
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

    private function prefix(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function number(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function employee(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function provider(): array
    {
        return [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function status(): array
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

    private function type(): array
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

    private function traceId(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function hash(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
        ];
    }

    private function hashType(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 20,
            ]),
        ];
    }

    private function message(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 1024,
            ]),
        ];
    }

    private function pdfPath(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 1024,
            ]),
        ];
    }

    private function xmlPath(): array
    {
        return [
            new Assert\Type([
                'type' => 'string',
            ]),
            new Assert\Length([
                'max' => 1024,
            ]),
        ];
    }

    private function parentId(): array
    {
        return [
            new Assert\Type([
                'type' => 'int',
            ]),
        ];
    }

    private function downloadedAt(): array
    {
        return [
            new Assert\DateTime(),
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

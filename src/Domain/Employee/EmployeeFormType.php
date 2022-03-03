<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Employee;

use App\Form\Type\Select2Type;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\Request\ReadAccountTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AccountType\UseCase\ReadAccountTypeUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\DocumentType\Request\ReadDocumentTypeRequest;
use FlexPHP\Bundle\LocationBundle\Domain\DocumentType\UseCase\ReadDocumentTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\Request\ReadEmployeeSubTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeSubType\UseCase\ReadEmployeeSubTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\Request\ReadEmployeeTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType\UseCase\ReadEmployeeTypeUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\PaymentMethod\Request\ReadPaymentMethodRequest;
use FlexPHP\Bundle\LocationBundle\Domain\PaymentMethod\UseCase\ReadPaymentMethodUseCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EmployeeFormType extends AbstractType
{
    private ReadDocumentTypeUseCase $readDocumentTypeUseCase;

    private ReadEmployeeTypeUseCase $readEmployeeTypeUseCase;

    private ReadEmployeeSubTypeUseCase $readEmployeeSubTypeUseCase;

    private ReadPaymentMethodUseCase $readPaymentMethodUseCase;

    private ReadAccountTypeUseCase $readAccountTypeUseCase;

    private UrlGeneratorInterface $router;

    public function __construct(
        ReadDocumentTypeUseCase $readDocumentTypeUseCase,
        ReadEmployeeTypeUseCase $readEmployeeTypeUseCase,
        ReadEmployeeSubTypeUseCase $readEmployeeSubTypeUseCase,
        ReadPaymentMethodUseCase $readPaymentMethodUseCase,
        ReadAccountTypeUseCase $readAccountTypeUseCase,
        UrlGeneratorInterface $router
    ) {
        $this->readDocumentTypeUseCase = $readDocumentTypeUseCase;
        $this->readEmployeeTypeUseCase = $readEmployeeTypeUseCase;
        $this->readEmployeeSubTypeUseCase = $readEmployeeSubTypeUseCase;
        $this->readPaymentMethodUseCase = $readPaymentMethodUseCase;
        $this->readAccountTypeUseCase = $readAccountTypeUseCase;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $documentTypeIdModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readDocumentTypeUseCase->execute(new ReadDocumentTypeRequest($value));

                if ($response->documentType->id()) {
                    $choices = [$response->documentType->name() => $value];
                }
            }

            $form->add('documentTypeId', Select2Type::class, [
                'label' => 'label.documentTypeId',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.document-types'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($documentTypeIdModifier) {
            if (!$event->getData()) {
                return null;
            }

            $documentTypeIdModifier($event->getForm(), $event->getData()->documentTypeId());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($documentTypeIdModifier): void {
            $documentTypeIdModifier($event->getForm(), (string)$event->getData()['documentTypeId'] ?: null);
        });

        $typeModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readEmployeeTypeUseCase->execute(new ReadEmployeeTypeRequest($value));

                if ($response->employeeType->id()) {
                    $choices = [$response->employeeType->id() => $value];
                }
            }

            $form->add('type', Select2Type::class, [
                'label' => 'label.type',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.employee-types'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($typeModifier) {
            if (!$event->getData()) {
                return null;
            }

            $typeModifier($event->getForm(), $event->getData()->type());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($typeModifier): void {
            $typeModifier($event->getForm(), (int)$event->getData()['type'] ?: null);
        });

        $subTypeModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readEmployeeSubTypeUseCase->execute(new ReadEmployeeSubTypeRequest($value));

                if ($response->employeeSubType->id()) {
                    $choices = [$response->employeeSubType->id() => $value];
                }
            }

            $form->add('subType', Select2Type::class, [
                'label' => 'label.subType',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.employee-sub-types'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($subTypeModifier) {
            if (!$event->getData()) {
                return null;
            }

            $subTypeModifier($event->getForm(), $event->getData()->subType());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($subTypeModifier): void {
            $subTypeModifier($event->getForm(), (int)$event->getData()['subType'] ?: null);
        });

        $paymentMethodModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readPaymentMethodUseCase->execute(new ReadPaymentMethodRequest($value));

                if ($response->paymentMethod->id()) {
                    $choices = [$response->paymentMethod->name() => $value];
                }
            }

            $form->add('paymentMethod', Select2Type::class, [
                'label' => 'label.paymentMethod',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.payment-methods'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($paymentMethodModifier) {
            if (!$event->getData()) {
                return null;
            }

            $paymentMethodModifier($event->getForm(), $event->getData()->paymentMethod());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($paymentMethodModifier): void {
            $paymentMethodModifier($event->getForm(), (string)$event->getData()['paymentMethod'] ?: null);
        });

        $accountTypeModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readAccountTypeUseCase->execute(new ReadAccountTypeRequest($value));

                if ($response->accountType->id()) {
                    $choices = [$response->accountType->id() => $value];
                }
            }

            $form->add('accountType', Select2Type::class, [
                'label' => 'label.accountType',
                'required' => false,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.account-types'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($accountTypeModifier) {
            if (!$event->getData()) {
                return null;
            }

            $accountTypeModifier($event->getForm(), $event->getData()->accountType());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($accountTypeModifier): void {
            $accountTypeModifier($event->getForm(), (string)$event->getData()['accountType'] ?: null);
        });

        $builder->add('documentTypeId', Select2Type::class, [
            'label' => 'label.documentTypeId',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.document-types'),
                'maxlength' => 3,
            ],
        ]);
        $builder->add('documentNumber', InputType\TextType::class, [
            'label' => 'label.documentNumber',
            'required' => true,
        ]);
        $builder->add('firstName', InputType\TextType::class, [
            'label' => 'label.firstName',
            'required' => true,
            'attr' => [
                'maxlength' => 80,
            ],
        ]);
        $builder->add('secondName', InputType\TextType::class, [
            'label' => 'label.secondName',
            'required' => false,
            'attr' => [
                'maxlength' => 80,
            ],
        ]);
        $builder->add('firstSurname', InputType\TextType::class, [
            'label' => 'label.firstSurname',
            'required' => true,
            'attr' => [
                'maxlength' => 80,
            ],
        ]);
        $builder->add('secondSurname', InputType\TextType::class, [
            'label' => 'label.secondSurname',
            'required' => false,
            'attr' => [
                'maxlength' => 80,
            ],
        ]);
        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.employee-types'),
            ],
        ]);
        $builder->add('subType', Select2Type::class, [
            'label' => 'label.subType',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.employee-sub-types'),
            ],
        ]);
        $builder->add('paymentMethod', Select2Type::class, [
            'label' => 'label.paymentMethod',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.payment-methods'),
                'maxlength' => 5,
            ],
        ]);
        $builder->add('accountType', Select2Type::class, [
            'label' => 'label.accountType',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.account-types'),
                'maxlength' => 3,
            ],
        ]);
        $builder->add('accountNumber', InputType\TextType::class, [
            'label' => 'label.accountNumber',
            'required' => false,
            'attr' => [
                'maxlength' => 80,
            ],
        ]);
        $builder->add('isActive', InputType\CheckboxType::class, [
            'label' => 'label.isActive',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'employee',
        ]);
    }
}

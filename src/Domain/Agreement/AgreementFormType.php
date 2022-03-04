<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Agreement;

use App\Form\Type\DatetimepickerType;
use App\Form\Type\Select2Type;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\Request\ReadAgreementPeriodRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementPeriod\UseCase\ReadAgreementPeriodUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\Request\ReadAgreementStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementStatus\UseCase\ReadAgreementStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\Request\ReadAgreementTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\AgreementType\UseCase\ReadAgreementTypeUseCase;
use FlexPHP\Bundle\LocationBundle\Domain\Currency\Request\ReadCurrencyRequest;
use FlexPHP\Bundle\LocationBundle\Domain\Currency\UseCase\ReadCurrencyUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AgreementFormType extends AbstractType
{
    private ReadEmployeeUseCase $readEmployeeUseCase;

    private ReadAgreementTypeUseCase $readAgreementTypeUseCase;

    private ReadAgreementPeriodUseCase $readAgreementPeriodUseCase;

    private ReadCurrencyUseCase $readCurrencyUseCase;

    private ReadAgreementStatusUseCase $readAgreementStatusUseCase;

    private UrlGeneratorInterface $router;

    public function __construct(
        ReadEmployeeUseCase $readEmployeeUseCase,
        ReadAgreementTypeUseCase $readAgreementTypeUseCase,
        ReadAgreementPeriodUseCase $readAgreementPeriodUseCase,
        ReadCurrencyUseCase $readCurrencyUseCase,
        ReadAgreementStatusUseCase $readAgreementStatusUseCase,
        UrlGeneratorInterface $router
    ) {
        $this->readEmployeeUseCase = $readEmployeeUseCase;
        $this->readAgreementTypeUseCase = $readAgreementTypeUseCase;
        $this->readAgreementPeriodUseCase = $readAgreementPeriodUseCase;
        $this->readCurrencyUseCase = $readCurrencyUseCase;
        $this->readAgreementStatusUseCase = $readAgreementStatusUseCase;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $employeeModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readEmployeeUseCase->execute(new ReadEmployeeRequest($value));

                if ($response->employee->id()) {
                    $choices = [$response->employee->getFullname() => $value];
                }
            }

            $form->add('employee', Select2Type::class, [
                'label' => 'label.employee',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.employees'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($employeeModifier) {
            if (!$event->getData()) {
                return null;
            }

            $employeeModifier($event->getForm(), $event->getData()->employee());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($employeeModifier): void {
            $employeeModifier($event->getForm(), (int)$event->getData()['employee'] ?: null);
        });

        $typeModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readAgreementTypeUseCase->execute(new ReadAgreementTypeRequest($value));

                if ($response->agreementType->id()) {
                    $choices = [$response->agreementType->name() => $value];
                }
            }

            $form->add('type', Select2Type::class, [
                'label' => 'label.type',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-types'),
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

        $periodModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readAgreementPeriodUseCase->execute(new ReadAgreementPeriodRequest($value));

                if ($response->agreementPeriod->id()) {
                    $choices = [$response->agreementPeriod->name() => $value];
                }
            }

            $form->add('period', Select2Type::class, [
                'label' => 'label.period',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-periods'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($periodModifier) {
            if (!$event->getData()) {
                return null;
            }

            $periodModifier($event->getForm(), $event->getData()->period());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($periodModifier): void {
            $periodModifier($event->getForm(), (string)$event->getData()['period'] ?: null);
        });

        $currencyModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readCurrencyUseCase->execute(new ReadCurrencyRequest($value));

                if ($response->currency->id()) {
                    $choices = [$response->currency->name() => $value];
                }
            }

            $form->add('currency', Select2Type::class, [
                'label' => 'label.currency',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.currencies'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($currencyModifier) {
            if (!$event->getData()) {
                return null;
            }

            $currencyModifier($event->getForm(), $event->getData()->currency());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($currencyModifier): void {
            $currencyModifier($event->getForm(), (string)$event->getData()['currency'] ?: null);
        });

        $statusModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readAgreementStatusUseCase->execute(new ReadAgreementStatusRequest($value));

                if ($response->agreementStatus->id()) {
                    $choices = [$response->agreementStatus->name() => $value];
                }
            }

            $form->add('status', Select2Type::class, [
                'label' => 'label.status',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-status'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($statusModifier) {
            if (!$event->getData()) {
                return null;
            }

            $statusModifier($event->getForm(), $event->getData()->status());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($statusModifier): void {
            $statusModifier($event->getForm(), (string)$event->getData()['status'] ?: null);
        });

        $builder->add('name', InputType\TextType::class, [
            'label' => 'label.name',
            'required' => true,
            'attr' => [
                'maxlength' => 255,
            ],
        ]);
        $builder->add('employee', Select2Type::class, [
            'label' => 'label.employee',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.employees'),
            ],
        ]);
        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-types'),
            ],
        ]);
        $builder->add('period', Select2Type::class, [
            'label' => 'label.period',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-periods'),
                'maxlength' => 2,
            ],
        ]);
        $builder->add('currency', Select2Type::class, [
            'label' => 'label.currency',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.currencies'),
                'maxlength' => 3,
            ],
        ]);
        $builder->add('salary', InputType\TextType::class, [
            'label' => 'label.salary',
            'required' => true,
        ]);
        $builder->add('healthPercentage', InputType\IntegerType::class, [
            'label' => 'label.healthPercentage',
            'required' => true,
        ]);
        $builder->add('pensionPercentage', InputType\IntegerType::class, [
            'label' => 'label.pensionPercentage',
            'required' => true,
        ]);
        $builder->add('integralSalary', InputType\CheckboxType::class, [
            'label' => 'label.integralSalary',
            'required' => false,
        ]);
        $builder->add('highRisk', InputType\CheckboxType::class, [
            'label' => 'label.highRisk',
            'required' => false,
        ]);
        $builder->add('initAt', DatetimepickerType::class, [
            'label' => 'label.initAt',
            'required' => true,
        ]);
        $builder->add('finishAt', DatetimepickerType::class, [
            'label' => 'label.finishAt',
            'required' => false,
        ]);
        $builder->add('status', Select2Type::class, [
            'label' => 'label.status',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-status'),
                'maxlength' => 2,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'agreement',
        ]);
    }
}

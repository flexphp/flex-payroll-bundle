<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Payroll;

use App\Form\Type\DatetimepickerType;
use App\Form\Type\Select2Type;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\ReadPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\ReadPayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\ReadPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\ReadPayrollTypeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\Request\ReadPayrollRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Payroll\UseCase\ReadPayrollUseCase;
use FlexPHP\Bundle\InvoiceBundle\Domain\Provider\Request\ReadProviderRequest;
use FlexPHP\Bundle\InvoiceBundle\Domain\Provider\UseCase\ReadProviderUseCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PayrollFormType extends AbstractType
{
    private ReadEmployeeUseCase $readEmployeeUseCase;

    private ReadProviderUseCase $readProviderUseCase;

    private ReadPayrollStatusUseCase $readPayrollStatusUseCase;

    private ReadPayrollTypeUseCase $readPayrollTypeUseCase;

    private ReadPayrollUseCase $readPayrollUseCase;

    private UrlGeneratorInterface $router;

    public function __construct(
        ReadEmployeeUseCase $readEmployeeUseCase,
        ReadProviderUseCase $readProviderUseCase,
        ReadPayrollStatusUseCase $readPayrollStatusUseCase,
        ReadPayrollTypeUseCase $readPayrollTypeUseCase,
        ReadPayrollUseCase $readPayrollUseCase,
        UrlGeneratorInterface $router
    ) {
        $this->readEmployeeUseCase = $readEmployeeUseCase;
        $this->readProviderUseCase = $readProviderUseCase;
        $this->readPayrollStatusUseCase = $readPayrollStatusUseCase;
        $this->readPayrollTypeUseCase = $readPayrollTypeUseCase;
        $this->readPayrollUseCase = $readPayrollUseCase;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $employeeModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readEmployeeUseCase->execute(new ReadEmployeeRequest($value));

                if ($response->employee->id()) {
                    $choices = [$response->employee->documentNumber() => $value];
                }
            }

            $form->add('employee', Select2Type::class, [
                'label' => 'label.employee',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.employees'),
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

        $providerModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readProviderUseCase->execute(new ReadProviderRequest($value));

                if ($response->provider->id()) {
                    $choices = [$response->provider->name() => $value];
                }
            }

            $form->add('provider', Select2Type::class, [
                'label' => 'label.provider',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.providers'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($providerModifier) {
            if (!$event->getData()) {
                return null;
            }

            $providerModifier($event->getForm(), $event->getData()->provider());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($providerModifier): void {
            $providerModifier($event->getForm(), (string)$event->getData()['provider'] ?: null);
        });

        $statusModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readPayrollStatusUseCase->execute(new ReadPayrollStatusRequest($value));

                if ($response->payrollStatus->id()) {
                    $choices = [$response->payrollStatus->name() => $value];
                }
            }

            $form->add('status', Select2Type::class, [
                'label' => 'label.status',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payroll-status'),
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

        $typeModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readPayrollTypeUseCase->execute(new ReadPayrollTypeRequest($value));

                if ($response->payrollType->id()) {
                    $choices = [$response->payrollType->name() => $value];
                }
            }

            $form->add('type', Select2Type::class, [
                'label' => 'label.type',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payroll-types'),
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
            $typeModifier($event->getForm(), (string)$event->getData()['type'] ?: null);
        });

        $parentIdModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readPayrollUseCase->execute(new ReadPayrollRequest($value));

                if ($response->payroll->id()) {
                    $choices = [$response->payroll->number() => $value];
                }
            }

            $form->add('parentId', Select2Type::class, [
                'label' => 'label.parentId',
                'required' => false,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payrolls'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($parentIdModifier) {
            if (!$event->getData()) {
                return null;
            }

            $parentIdModifier($event->getForm(), $event->getData()->parentId());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($parentIdModifier): void {
            $parentIdModifier($event->getForm(), (int)$event->getData()['parentId'] ?: null);
        });

        $builder->add('prefix', InputType\TextType::class, [
            'label' => 'label.prefix',
            'required' => true,
        ]);
        $builder->add('number', InputType\IntegerType::class, [
            'label' => 'label.number',
            'required' => true,
        ]);
        $builder->add('employee', Select2Type::class, [
            'label' => 'label.employee',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.employees'),
            ],
        ]);
        $builder->add('provider', Select2Type::class, [
            'label' => 'label.provider',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.providers'),
            ],
        ]);
        $builder->add('status', Select2Type::class, [
            'label' => 'label.status',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payroll-status'),
                'maxlength' => 2,
            ],
        ]);
        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payroll-types'),
                'maxlength' => 3,
            ],
        ]);
        $builder->add('traceId', InputType\TextType::class, [
            'label' => 'label.traceId',
            'required' => false,
        ]);
        $builder->add('hash', InputType\TextType::class, [
            'label' => 'label.hash',
            'required' => false,
        ]);
        $builder->add('hashType', InputType\TextType::class, [
            'label' => 'label.hashType',
            'required' => false,
            'attr' => [
                'maxlength' => 20,
            ],
        ]);
        $builder->add('message', InputType\TextType::class, [
            'label' => 'label.message',
            'required' => false,
            'attr' => [
                'maxlength' => 1024,
            ],
        ]);
        $builder->add('pdfPath', InputType\TextType::class, [
            'label' => 'label.pdfPath',
            'required' => false,
            'attr' => [
                'maxlength' => 1024,
            ],
        ]);
        $builder->add('xmlPath', InputType\TextType::class, [
            'label' => 'label.xmlPath',
            'required' => false,
            'attr' => [
                'maxlength' => 1024,
            ],
        ]);
        $builder->add('parentId', Select2Type::class, [
            'label' => 'label.parentId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payrolls'),
            ],
        ]);
        $builder->add('downloadedAt', DatetimepickerType::class, [
            'label' => 'label.downloadedAt',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'payroll',
        ]);
    }
}

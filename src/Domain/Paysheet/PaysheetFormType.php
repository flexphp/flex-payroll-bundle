<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\Paysheet;

use App\Form\Type\DatetimepickerType;
use App\Form\Type\Select2Type;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\Request\ReadAgreementRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Agreement\UseCase\ReadAgreementUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\Request\ReadEmployeeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\Employee\UseCase\ReadEmployeeUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\Request\ReadPayrollStatusRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus\UseCase\ReadPayrollStatusUseCase;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\Request\ReadPayrollTypeRequest;
use FlexPHP\Bundle\PayrollBundle\Domain\PayrollType\UseCase\ReadPayrollTypeUseCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaysheetFormType extends AbstractType
{
    private ReadPayrollTypeUseCase $readPayrollTypeUseCase;

    private ReadEmployeeUseCase $readEmployeeUseCase;

    private ReadAgreementUseCase $readAgreementUseCase;

    private ReadPayrollStatusUseCase $readPayrollStatusUseCase;

    private UrlGeneratorInterface $router;

    public function __construct(
        ReadPayrollTypeUseCase $readPayrollTypeUseCase,
        ReadEmployeeUseCase $readEmployeeUseCase,
        ReadAgreementUseCase $readAgreementUseCase,
        ReadPayrollStatusUseCase $readPayrollStatusUseCase,
        UrlGeneratorInterface $router
    ) {
        $this->readPayrollTypeUseCase = $readPayrollTypeUseCase;
        $this->readEmployeeUseCase = $readEmployeeUseCase;
        $this->readAgreementUseCase = $readAgreementUseCase;
        $this->readPayrollStatusUseCase = $readPayrollStatusUseCase;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typeModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readPayrollTypeUseCase->execute(new ReadPayrollTypeRequest($value));

                if ($response->paysheetType->id()) {
                    $choices = [$response->paysheetType->name() => $value];
                }
            }

            $form->add('type', Select2Type::class, [
                'label' => 'label.type',
                'required' => true,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('paysheets.find.payroll-types'),
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

        $employeeIdModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readEmployeeUseCase->execute(new ReadEmployeeRequest($value));

                if ($response->employee->id()) {
                    $choices = [$response->employee->name() => $value];
                }
            }

            $form->add('employeeId', Select2Type::class, [
                'label' => 'label.employeeId',
                'required' => false,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('paysheets.find.employees'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($employeeIdModifier) {
            if (!$event->getData()) {
                return null;
            }

            $employeeIdModifier($event->getForm(), $event->getData()->employeeId());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($employeeIdModifier): void {
            $employeeIdModifier($event->getForm(), (int)$event->getData()['employeeId'] ?: null);
        });

        $agreementIdModifier = function (FormInterface $form, ?int $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readAgreementUseCase->execute(new ReadAgreementRequest($value));

                if ($response->agreement->id()) {
                    $choices = [$response->agreement->placa() => $value];
                }
            }

            $form->add('agreementId', Select2Type::class, [
                'label' => 'label.agreementId',
                'required' => false,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('paysheets.find.agreements'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($agreementIdModifier) {
            if (!$event->getData()) {
                return null;
            }

            $agreementIdModifier($event->getForm(), $event->getData()->agreementId());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($agreementIdModifier): void {
            $agreementIdModifier($event->getForm(), (int)$event->getData()['agreementId'] ?: null);
        });

        $statusIdModifier = function (FormInterface $form, ?string $value): void {
            $choices = null;

            if (!empty($value)) {
                $response = $this->readPayrollStatusUseCase->execute(new ReadPayrollStatusRequest($value));

                if ($response->paysheetStatus->id()) {
                    $choices = [$response->paysheetStatus->name() => $value];
                }
            }

            $form->add('statusId', Select2Type::class, [
                'label' => 'label.statusId',
                'required' => false,
                'attr' => [
                    'data-autocomplete-url' => $this->router->generate('paysheets.find.payroll-status'),
                ],
                'choices' => $choices,
                'data' => $value,
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($statusIdModifier) {
            if (!$event->getData()) {
                return null;
            }

            $statusIdModifier($event->getForm(), $event->getData()->statusId());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($statusIdModifier): void {
            $statusIdModifier($event->getForm(), (string)$event->getData()['statusId'] ?: null);
        });

        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => true,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('paysheets.find.payroll-types'),
                'maxlength' => 2,
            ],
        ]);
        $builder->add('employeeId', Select2Type::class, [
            'label' => 'label.employeeId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('paysheets.find.employees'),
            ],
        ]);
        $builder->add('agreementId', Select2Type::class, [
            'label' => 'label.agreementId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('paysheets.find.agreements'),
            ],
        ]);
        $builder->add('kilometers', InputType\IntegerType::class, [
            'label' => 'label.kilometers',
            'required' => false,
        ]);
        $builder->add('kilometersToChange', InputType\IntegerType::class, [
            'label' => 'label.kilometersToChange',
            'required' => false,
        ]);
        $builder->add('discount', InputType\TextType::class, [
            'label' => 'label.discount',
            'required' => true,
        ]);
        $builder->add('subtotal', InputType\TextType::class, [
            'label' => 'label.subtotal',
            'required' => true,
        ]);
        $builder->add('taxes', InputType\TextType::class, [
            'label' => 'label.taxes',
            'required' => true,
        ]);
        $builder->add('total', InputType\TextType::class, [
            'label' => 'label.total',
            'required' => true,
        ]);
        $builder->add('notes', InputType\TextareaType::class, [
            'label' => 'label.notes',
            'required' => false,
        ]);
        $builder->add('totalPaid', InputType\TextType::class, [
            'label' => 'label.totalPaid',
            'required' => true,
        ]);
        $builder->add('paidAt', DatetimepickerType::class, [
            'label' => 'label.paidAt',
            'required' => false,
        ]);
        $builder->add('statusId', Select2Type::class, [
            'label' => 'label.statusId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('paysheets.find.payroll-status'),
                'maxlength' => 2,
            ],
        ]);
        $builder->add('paysheetNotes', InputType\TextareaType::class, [
            'label' => 'label.paysheetNotes',
            'required' => false,
        ]);
        $builder->add('expiratedAt', DatetimepickerType::class, [
            'label' => 'label.expiratedAt',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'paysheet',
        ]);
    }
}

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

use App\Form\Type\DatefinishpickerType;
use App\Form\Type\DatestartpickerType;
use App\Form\Type\DatetimepickerType;
use App\Form\Type\Select2Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AgreementFilterFormType extends AbstractType
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', InputType\IntegerType::class, [
            'label' => 'label.id',
            'required' => false,
        ]);

        $builder->add('name', InputType\TextType::class, [
            'label' => 'label.name',
            'required' => false,
        ]);

        $builder->add('employee', Select2Type::class, [
            'label' => 'label.employee',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.employees'),
            ],
        ]);

        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-types'),
            ],
        ]);

        $builder->add('period', Select2Type::class, [
            'label' => 'label.period',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-periods'),
            ],
        ]);

        $builder->add('currency', Select2Type::class, [
            'label' => 'label.currency',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.currencies'),
            ],
        ]);

        $builder->add('salary', InputType\TextType::class, [
            'label' => 'label.salary',
            'required' => false,
        ]);

        $builder->add('healthPercentage', InputType\IntegerType::class, [
            'label' => 'label.healthPercentage',
            'required' => false,
        ]);

        $builder->add('pensionPercentage', InputType\IntegerType::class, [
            'label' => 'label.pensionPercentage',
            'required' => false,
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
            'required' => false,
        ]);

        $builder->add('finishAt', DatetimepickerType::class, [
            'label' => 'label.finishAt',
            'required' => false,
        ]);

        $builder->add('status', Select2Type::class, [
            'label' => 'label.status',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.agreements.find.agreement-status'),
            ],
        ]);

        $builder->add('createdAt_START', DatestartpickerType::class, [
            'label' => 'filter.createdAtStart',
        ]);

        $builder->add('createdAt_END', DatefinishpickerType::class, [
            'label' => 'filter.createdAtEnd',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'agreement',
        ]);
    }
}

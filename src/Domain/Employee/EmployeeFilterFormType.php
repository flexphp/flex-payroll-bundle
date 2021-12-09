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

use App\Form\Type\DatefinishpickerType;
use App\Form\Type\DatestartpickerType;
use App\Form\Type\Select2Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EmployeeFilterFormType extends AbstractType
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

        $builder->add('documentTypeId', Select2Type::class, [
            'label' => 'label.documentTypeId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.document-types'),
            ],
        ]);

        $builder->add('documentNumber', InputType\TextType::class, [
            'label' => 'label.documentNumber',
            'required' => false,
        ]);

        $builder->add('firstName', InputType\TextType::class, [
            'label' => 'label.firstName',
            'required' => false,
        ]);

        $builder->add('secondName', InputType\TextType::class, [
            'label' => 'label.secondName',
            'required' => false,
        ]);

        $builder->add('firstSurname', InputType\TextType::class, [
            'label' => 'label.firstSurname',
            'required' => false,
        ]);

        $builder->add('secondSurname', InputType\TextType::class, [
            'label' => 'label.secondSurname',
            'required' => false,
        ]);

        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.employee-types'),
            ],
        ]);

        $builder->add('subType', Select2Type::class, [
            'label' => 'label.subType',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.employee-sub-types'),
            ],
        ]);

        $builder->add('paymentMethod', Select2Type::class, [
            'label' => 'label.paymentMethod',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.payment-methods'),
            ],
        ]);

        $builder->add('accountType', Select2Type::class, [
            'label' => 'label.accountType',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.employees.find.account-types'),
            ],
        ]);

        $builder->add('accountNumber', InputType\TextType::class, [
            'label' => 'label.accountNumber',
            'required' => false,
        ]);

        $builder->add('isActive', InputType\CheckboxType::class, [
            'label' => 'label.isActive',
            'required' => false,
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
            'translation_domain' => 'employee',
        ]);
    }
}

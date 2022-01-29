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

use App\Form\Type\DatefinishpickerType;
use App\Form\Type\DatestartpickerType;
use App\Form\Type\Select2Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaysheetFilterFormType extends AbstractType
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', InputType\NumberType::class, [
            'label' => 'label.id',
            'required' => false,
        ]);

        $builder->add('employeeId', Select2Type::class, [
            'label' => 'label.employeeId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.paysheets.find.employees'),
            ],
        ]);

        $builder->add('agreementId', Select2Type::class, [
            'label' => 'label.agreementId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.paysheets.find.agreements'),
            ],
        ]);

        $builder->add('statusId', Select2Type::class, [
            'label' => 'label.statusId',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.paysheets.find.payroll-status'),
                'maxlength' => 2,
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
            'translation_domain' => 'paysheet',
        ]);
    }
}

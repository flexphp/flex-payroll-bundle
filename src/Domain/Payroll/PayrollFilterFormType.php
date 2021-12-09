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

use App\Form\Type\DatefinishpickerType;
use App\Form\Type\DatestartpickerType;
use App\Form\Type\DatetimepickerType;
use App\Form\Type\Select2Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PayrollFilterFormType extends AbstractType
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

        $builder->add('prefix', InputType\TextType::class, [
            'label' => 'label.prefix',
            'required' => false,
        ]);

        $builder->add('number', InputType\IntegerType::class, [
            'label' => 'label.number',
            'required' => false,
        ]);

        $builder->add('employee', Select2Type::class, [
            'label' => 'label.employee',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.employees'),
            ],
        ]);

        $builder->add('provider', Select2Type::class, [
            'label' => 'label.provider',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.providers'),
            ],
        ]);

        $builder->add('status', Select2Type::class, [
            'label' => 'label.status',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payroll-status'),
            ],
        ]);

        $builder->add('type', Select2Type::class, [
            'label' => 'label.type',
            'required' => false,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('flexphp.payroll.payrolls.find.payroll-types'),
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
        ]);

        $builder->add('message', InputType\TextType::class, [
            'label' => 'label.message',
            'required' => false,
        ]);

        $builder->add('pdfPath', InputType\TextType::class, [
            'label' => 'label.pdfPath',
            'required' => false,
        ]);

        $builder->add('xmlPath', InputType\TextType::class, [
            'label' => 'label.xmlPath',
            'required' => false,
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
            'translation_domain' => 'payroll',
        ]);
    }
}

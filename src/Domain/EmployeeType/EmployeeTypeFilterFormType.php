<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\EmployeeType;

use App\Form\Type\DatefinishpickerType;
use App\Form\Type\DatestartpickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EmployeeTypeFilterFormType extends AbstractType
{
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

        $builder->add('code', InputType\TextType::class, [
            'label' => 'label.code',
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
            'translation_domain' => 'employeeType',
        ]);
    }
}

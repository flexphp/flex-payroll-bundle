<?php declare(strict_types=1);
/*
 * This file is part of FlexPHP.
 *
 * (c) Freddie Gar <freddie.gar@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexPHP\Bundle\PayrollBundle\Domain\PayrollStatus;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as InputType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PayrollStatusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', InputType\TextType::class, [
            'label' => 'label.id',
            'required' => true,
            'attr' => [
                'maxlength' => 2,
            ],
        ]);
        $builder->add('name', InputType\TextType::class, [
            'label' => 'label.name',
            'required' => true,
            'attr' => [
                'maxlength' => 80,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'payrollStatus',
        ]);
    }
}

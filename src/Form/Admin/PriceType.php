<?php

namespace App\Form\Admin;

use App\Entity\Item;
use App\Entity\Price;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'duration',
                NumberType::class,
                [
                    'required' => true,
                    'label'    => 'Duration in day(s)',
                    'help'     => 'Starting from',
                    'attr'     => [
                        'min'  => 0,
                        'step' => 0.5,
                        'placeholder' => '0.5',
                        'class' => 'form-control-sm'
                    ],
                    'row_attr' => [
                        'class' => 'mb-1'
                    ],
                ]
            )
            ->add(
                'amount',
                MoneyType::class,
                [
                    'required' => true,
                    'label'    => 'Amount',
                    'help'     => 'Amount per day',
                    'divisor'  => 100,
                    'attr'     => [
                        'min'  => 0,
                        'step' => 1,
                        'placeholder' => '1',
                    ],
                    'row_attr' => [
                        'class' => 'mb-1'
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Price::class,
        ]);
    }
}

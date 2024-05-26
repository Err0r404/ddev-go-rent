<?php

namespace App\Form\App;

use App\Model\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'fromDate',
                DateType::class,
                [
                    'required' => true,
                    'label'    => 'Pickup Date',
                    'widget'   => 'single_text',
                    'html5'    => false,
                    'input'    => 'datetime_immutable',
                    'format'   => 'dd/MM/yyyy',
                    // 'attr'     => [
                    //     'class' => 'flatpickr form-control-sm',
                    // ],
                    // 'label_attr' => [
                    //     'class' => 'col-form-label-sm col-sm-4',
                    // ],
                ]
            )
            ->add(
                'fromTime',
                ChoiceType::class,
                [
                    'required'      => true,
                    'label'         => '&nbsp;',
                    'choices'       => Search::FROM_TIMES,
                    'label_html'    => true,
                    'label_attr'    => [
                        'class' => 'hide-required',
                    ],
                    'error_mapping' => [
                        '.' => 'fromDate',
                    ],
                ]
            )
            ->add(
                'toDate',
                DateType::class,
                [
                    'required' => true,
                    'label'    => 'Return Date',
                    'widget'   => 'single_text',
                    'html5'    => false,
                    'input'    => 'datetime_immutable',
                    'format'   => 'dd/MM/yyyy',
                    // 'attr'     => [
                    //     'class' => 'flatpickr form-control-sm',
                    // ],
                    // 'label_attr' => [
                    //     'class' => 'col-form-label-sm col-sm-4',
                    // ],
                ]
            )
            ->add(
                'toTime',
                ChoiceType::class,
                [
                    'required'      => true,
                    'label'         => '&nbsp;',
                    'choices'       => Search::TO_TIMES,
                    'label_html'    => true,
                    'label_attr'    => [
                        'class' => 'hide-required',
                    ],
                    'error_mapping' => [
                        '.' => 'toDate',
                    ],
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class'      => Search::class,
            'csrf_protection' => false,
            'method'          => 'GET',
        ]);
    }
    
    public function getBlockPrefix(): string
    {
        return '';
    }
}

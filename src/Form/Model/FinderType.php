<?php

namespace App\Form\Model;

use App\Model\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'search',
                SearchType::class,
                [
                    'required' => false,
                    'label'    => false,
                    'attr'     => [
                        'placeholder'  => 'Search',
                        'autocomplete' => 'off',
                        'class'        => 'w-250px ps-13',
                    ],
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Finder::class,
            'method'             => 'GET',
            'allow_extra_fields' => true,
            'csrf_protection'    => false,
        ]);
    }
    
    public function getBlockPrefix(): string
    {
        return '';
    }
}

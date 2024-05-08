<?php

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'required'        => true,
                    'invalid_message' => 'The password fields must match',
                    'options'         => [
                        'attr' => [
                            'required'     => true,
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'first_options'   => [
                        'label'    => 'Password',
                        'attr'     => [
                            'autocomplete' => 'new-password',
                            'placeholder'  => 'Password',
                        ],
                        'row_attr' => [
                            'class' => 'form-floating mb-3',
                        ],
                    ],
                    'second_options'  => [
                        'label'    => 'Confirm password',
                        'help'     => 'Password must contain at least 12 characters with a mix of lowercase and uppercase letters, numbers & symbols',
                        'attr'     => [
                            'autocomplete' => 'new-password',
                            'placeholder'  => 'Confirm password',
                        ],
                        'row_attr' => [
                            'class' => 'form-floating mb-3',
                        ],
                    ],
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => User::class,
            'validation_groups' => ['UserCreatePlainPassword'],
        ]);
    }
}

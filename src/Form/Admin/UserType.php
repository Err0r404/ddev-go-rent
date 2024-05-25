<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function __construct(private readonly ArrayToStringTransformer $arrayToStringTransformer)
    {
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $builder->getData();
        
        $passwordRequired = false;
        $passwordHelp     = 'Password must contain at least 12 characters with a mix of lowercase and uppercase letters, numbers & symbols.<br>' .
                            'Leave empty to keep the current password.';
        $passwordHelpHtml = true;
        if (!$user || !$user->getId()) {
            $passwordRequired = true;
            $passwordHelp     = 'Password must contain at least 12 characters with a mix of lowercase and uppercase letters, numbers & symbols.';
            $passwordHelpHtml = false;
        }
        
        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'First name',
                    'attr'     => [
                        'class'    => 'form-control-solid',
                        'readonly' => (bool)$options['readonly'],
                    ],
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'Last name',
                    'attr'     => [
                        'class'    => 'form-control-solid',
                        'readonly' => (bool)$options['readonly'],
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                    'label'    => 'Email',
                    'attr'     => [
                        'class'    => 'form-control-solid',
                        'readonly' => (bool)$options['readonly'],
                    ],
                ]
            )
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'required' => true,
                    'label'    => 'Role',
                    'choices'  => $options['roles'],
                    'multiple' => false,
                    'attr'     => [
                        'class'            => 'form-select-solid',
                        'data-kt-select2'  => 'true',
                        'data-placeholder' => 'Role',
                        'data-allow-clear' => 'false',
                        'data-hide-search' => 'true',
                        'readonly'         => (bool)$options['readonly'],
                    ],
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type'           => PasswordType::class,
                    'required'       => $passwordRequired,
                    'options'        => [
                        'attr' => [
                            'required'     => $passwordRequired,
                            'autocomplete' => 'new-password',
                            'readonly'     => (bool)$options['readonly'],
                        ],
                    ],
                    'first_options'  => [
                        'label'     => 'Password',
                        'help'      => $passwordHelp,
                        'help_html' => $passwordHelpHtml,
                        'attr'      => [
                            'class'        => 'form-control-solid',
                            'autocomplete' => 'new-password',
                            'readonly'     => (bool)$options['readonly'],
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirm password',
                        'attr'  => [
                            'class'        => 'form-control-solid',
                            'autocomplete' => 'new-password',
                            'readonly'     => (bool)$options['readonly'],
                        ],
                    ],
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'label'    => 'Enabled',
                    'attr'     => [
                        'class'    => 'form-check-solid',
                        'readonly' => (bool)$options['readonly'],
                    ],
                ]
            )
        ;
        
        if ($options['edit_password'] === false) {
            $builder->remove('plainPassword');
        }
        
        $builder->get("roles")->addModelTransformer($this->arrayToStringTransformer);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => User::class,
            'roles'         => User::ROLES,
            'edit_password' => true,
            'readonly'      => false,
        ]);
    }
}

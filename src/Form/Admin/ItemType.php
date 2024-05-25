<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Item;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'category',
                EntityType::class,
                [
                    'required'      => true,
                    'class'         => Category::class,
                    'choice_label'  => 'name',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository
                            ->createQueryBuilder('c')
                            ->andWhere('c.deletedAt IS NULL')
                            ->orderBy('c.name', 'ASC')
                        ;
                    },
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'label'    => 'Name',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'label'    => 'Description',
                    'attr'     => ['rows' => 5],
                ]
            )
            ->add(
                'prices',
                CollectionType::class,
                [
                    'required'      => true,
                    'label'         => false,
                    'entry_type'    => PriceType::class,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,
                    'entry_options' => [
                        'label' => false,
                    ],
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}

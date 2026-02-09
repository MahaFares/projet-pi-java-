<?php

namespace App\Form;

use App\Entity\ActivityCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'help' => 'Nom de la catégorie (3-100 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Randonnée'],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'help' => 'Description optionnelle (max 1000 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Description brève', 'rows' => 4],
            ])
            ->add('icon', TextType::class, [
                'required' => false,
                'help' => 'Emoji ou icône (max 50 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 🏔️'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivityCategory::class,
        ]);
    }
}

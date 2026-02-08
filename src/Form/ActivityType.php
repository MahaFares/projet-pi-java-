<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivityCategory;
use App\Entity\Guide;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('price')
            ->add('durationMinutes')
            ->add('location')
            ->add('maxParticipants')
            ->add('image')
            ->add('isActive')
            ->add('category', EntityType::class, [
                'class' => ActivityCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une catégorie',
            ])
            ->add('guide', EntityType::class, [
                'class' => Guide::class,
                'choice_label' => function (Guide $g) {
                    return $g->getFirstName() . ' ' . $g->getLastName();
                },
                'placeholder' => 'Choisir un guide (optionnel)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}

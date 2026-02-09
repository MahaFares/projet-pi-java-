<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivitySchedule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'required' => true,
                'help' => 'Date et heure de début (doit être dans le futur)',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'type' => 'datetime-local'],
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => true,
                'help' => 'Date et heure de fin (après la date de début)',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control', 'type' => 'datetime-local'],
            ])
            ->add('availableSpots', IntegerType::class, [
                'required' => true,
                'help' => 'Nombre de places disponibles (minimum 1)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 10'],
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'title',
                'placeholder' => 'Choisir une activité',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActivitySchedule::class,
        ]);
    }
}

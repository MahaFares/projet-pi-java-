<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivitySchedule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt')
            ->add('endAt')
            ->add('availableSpots')
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'choice_label' => 'title',
                'placeholder' => 'Choisir une activité',
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

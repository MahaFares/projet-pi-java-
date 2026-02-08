<?php

namespace App\Form;

use App\Entity\CategorieHebergement;
use App\Entity\Equipement;
use App\Entity\Hebergement;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HebergementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('adresse')
            ->add('ville')
            ->add('nbEtoiles')
            ->add('imagePrincipale')
            ->add('labelEco')
            ->add('latitude')
            ->add('longitude')
            ->add('actif')
            ->add('categorie', EntityType::class, [
                'class' => CategorieHebergement::class,
                'choice_label' => 'id',
            ])
            ->add('equipements', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('propietaire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hebergement::class,
        ]);
    }
}

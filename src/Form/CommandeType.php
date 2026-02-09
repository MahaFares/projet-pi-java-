<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Enum\TypeCommande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'label' => 'Produit',
                'placeholder' => '-- Choisir un produit --',
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Positive(['message' => 'La quantité doit être strictement positive.']),
                ],
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix unitaire',
                'scale' => 2,
                'constraints' => [new NotBlank(['message' => 'Le prix unitaire est obligatoire.'])],
            ])
            ->add('total', NumberType::class, [
                'label' => 'Total',
                'scale' => 2,
                'constraints' => [new NotBlank(['message' => 'Le total est obligatoire.'])],
            ])
            ->add('dateCommande', DateTimeType::class, [
                'label' => 'Date de commande',
                'widget' => 'single_text',
                'constraints' => [new NotBlank(['message' => 'La date de commande est obligatoire.'])],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'commande_item',
        ]);
    }
}

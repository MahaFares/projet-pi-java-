<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Paiement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => fn (Commande $c) => sprintf('#%d - %s', $c->getIdCommande(), $c->getProduit()?->getNom() ?? 'N/A'),
                'label' => 'Commande',
                'placeholder' => '-- Choisir une commande --',
                'constraints' => [new NotBlank(['message' => 'La commande est obligatoire.'])],
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Le montant est obligatoire.']),
                    new GreaterThan(['value' => 0, 'message' => 'Le montant doit être strictement positif.']),
                ],
            ])
            ->add('methodePaiement', TextType::class, [
                'label' => 'Méthode de paiement',
                'constraints' => [
                    new NotBlank(['message' => 'La méthode de paiement est obligatoire.']),
                    new Length(['max' => 50]),
                ],
            ])
           
            ->add('datePaiement', DateTimeType::class, [
                'label' => 'Date de paiement',
                'widget' => 'single_text',
                'constraints' => [new NotBlank(['message' => 'La date de paiement est obligatoire.'])],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'paiement_item',
        ]);
    }
}

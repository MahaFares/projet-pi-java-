<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit est obligatoire.']),
                    new Length(['max' => 150]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire.']),
                    new GreaterThanOrEqual(['value' => 0, 'message' => 'Le prix ne peut pas être négatif.']),
                ],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'constraints' => [
                    new NotBlank(['message' => 'Le stock est obligatoire.']),
                    new GreaterThanOrEqual(['value' => 0, 'message' => 'Le stock ne peut pas être négatif.']),
                ],
            ])
            ->add('image', TextType::class, [
                'label' => 'Image (chemin ou URL)',
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'produit_item',
        ]);
    }
}

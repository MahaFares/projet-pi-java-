<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Choisir' => '',
                    'A louer' => 'A louer',
                    'A vendre' => 'A vendre',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'état est obligatoire.']),
                    new Choice([
                        'choices' => ['A louer', 'A vendre'],
                        'message' => 'Vous devez choisir soit "A louer" soit "A vendre".'
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'categorie_item',
        ]);
    }
}
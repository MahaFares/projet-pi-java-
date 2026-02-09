<?php

namespace App\Form;

use App\Entity\ActivityCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ->add('icon', FileType::class, [
                'required' => false,
                'help' => 'Téléchargez une icône (JPG, PNG ou GIF - max 1 MB)',
                'attr' => ['class' => 'form-control', 'accept' => 'image/jpeg,image/png,image/gif'],
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une icône valide (JPG, PNG ou GIF)',
                    ])
                ],
                'mapped' => false,
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

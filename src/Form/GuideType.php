<?php

namespace App\Form;

use App\Entity\Guide;
use Symfony\Component\Form\AbstractType;
<<<<<<< HEAD
=======
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<<<<<<< HEAD
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')
            ->add('bio')
            ->add('rating')
            ->add('photo')
=======
            ->add('firstName', TextType::class, [
                'required' => true,
                'help' => 'Prénom du guide (2-120 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Jean'],
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'help' => 'Nom du guide (2-120 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Dupont'],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'help' => 'Adresse email (unique, max 150 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'exemple@email.com'],
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'help' => 'Numéro de téléphone (10-30 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => '+33 1 23 45 67 89'],
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
                'help' => 'Biographie du guide (max 5000 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Parlez de votre expérience...', 'rows' => 5],
            ])
            ->add('rating', NumberType::class, [
                'required' => false,
                'help' => 'Note du guide (0-5 étoiles)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: 4.5', 'min' => '0', 'max' => '5', 'step' => '0.5'],
            ])
            ->add('photo', TextType::class, [
                'required' => false,
                'help' => 'URL de la photo du guide (max 255 caractères)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'https://example.com/photo.jpg'],
            ])
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guide::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Valid;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la publication',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'attr' => ['readonly' => true],
            ])
            ->add('imageChoice', ChoiceType::class, [
                'label' => 'Choisissez une méthode pour l\'image',
                'choices' => [
                    'Télécharger une image' => 'upload',
                    'Entrer une URL d\'image' => 'url',
                ],
                'expanded' => true,  // Radio buttons
                'multiple' => false,
                'data' => 'upload',  // Default option
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Télécharger une image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG, GIF).',
                    ])
                ],
            ])
            ->add('image', UrlType::class, [
                'label' => 'URL de l\'image',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
            'validation_groups' => ['Default'],
        ]);
    }
}

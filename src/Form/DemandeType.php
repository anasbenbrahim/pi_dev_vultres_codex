<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\Offer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType; 
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('service', null, [
            'label' => 'Service',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez le service',
            ],
        ])
        ->add('offer', EntityType::class, [
            'class' => Offer::class,
            'label' => 'Offre',
            'attr' => [
                'class' => 'form-control',
            ],
            'placeholder' => 'Sélectionnez une offre',
        ])
        ->add('date_demande', DateType::class, [
            'label' => 'Date Demande',
            'widget' => 'single_text', // Use a single text input for the date
            'html5' => true, // Use HTML5 date input
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez la date de demande',
            ],
        ])
        ->add('cvFile', VichFileType::class, [
            'label' => 'CV (PDF file)',
            'required' => true,
            'attr' => [
                'class' => 'form-control',
                'accept' => 'application/pdf', // Restrict to PDF files
            ],
        ])
        ->add('date_demande', DateType::class, [
            'label' => 'Date de demande',
            'widget' => 'single_text',
            'html5' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Sélectionnez la date de demande',
            ],
        ])
        ->add('phoneNumber', TextType::class, [
            'label' => 'Phone Number',
            'required' => true,
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}

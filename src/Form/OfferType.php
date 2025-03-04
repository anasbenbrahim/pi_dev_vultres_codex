<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;  // Added for proper text fields
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom de l\'offre',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez le nom de l\'offre',
            ],
            'required' => true,
        ])
        ->add('domain', TextType::class, [  // Changed to TextType for domain
            'label' => 'Domaine',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez le domaine',
            ],
            'required' => true,
        ])
        ->add('dateOffer', DateType::class, [
            'label' => 'Date de l\'offre',
            'widget' => 'single_text',
            'html5' => true,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez la date de l\'offre',
            ],
        ])
        ->add('description', TextType::class, [  // Changed to TextType for description
            'label' => 'Description',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez la description',
            ],
            'required' => true,
        ])
        ->add('nb_Places', IntegerType::class, [  // Changed to IntegerType for nbPlaces
            'label' => 'Nombre de places',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez le nombre de places',
            ],
            'required' => true,
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}

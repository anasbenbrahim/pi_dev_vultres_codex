<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('domain')
            ->add('date_offer', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
                'invalid_message' => 'La date doit être valide.',
                'attr' => ['class' => 'form-control datepicker']
            ])
            ->add('description')
            ->add('nb_places')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}

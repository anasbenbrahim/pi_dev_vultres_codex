<?php

namespace App\Form;

use App\Entity\Demande;
use App\Entity\Offer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service')
            ->add('date_demande', null, [
                'widget' => 'single_text',
            ])
            ->add('CV')
            ->add('offer', EntityType::class, [
                'class' => Offer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}

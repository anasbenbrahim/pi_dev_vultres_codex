<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Employee;
use App\Entity\Fermier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('domain')
            ->add('date_offer', null, [
                'widget' => 'single_text'
            ])
            ->add('description')
            ->add('nb_places')
            /*->add('fermieroffer', EntityType::class, [
                'class' => fermier::class,
'choice_label' => 'id',
            ])
            ->add('employeeoffer', EntityType::class, [
                'class' => employee::class,
'choice_label' => 'id',
'multiple' => true,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}

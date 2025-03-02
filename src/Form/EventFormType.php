<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Entity\Event;
use App\Enum\EventTyp;
use App\Enum\EventType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType // ✅ Vérifie bien que c'est EventFormType ici
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('descr')
            ->add('date')
            ->add('photo', FileType::class, [
                'label' => 'photo du produit',
                'mapped' => false, // ❗ Important: Not mapped to the entity (handle manually in controller)
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'simple' => EventType::SIMPLE,
                    'workshop' => EventType::WORKSHOP,
                    'Festival' => EventType::FESTIVAL,
                ],
                'choice_label' => fn($choice) => $choice->value,
                'expanded' => false,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
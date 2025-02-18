<?php

namespace App\Form;

use App\Entity\CategoryEquipements;
use App\Entity\Equipements;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddEquipementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('quantite')
            ->add('prix')
            ->add('description',TextareaType::class)
            ->add('image',FileType::class,['data_class'=>null])
            ->add('category', EntityType::class, [
                'class' => CategoryEquipements::class,
                'choice_label' => 'type',
            ])
            ->add('save',SubmitType::class,['label'=>'Ajouter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipements::class,
        ]);
    }
}

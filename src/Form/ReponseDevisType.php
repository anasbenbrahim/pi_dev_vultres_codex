<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\ReponseDevis;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseDevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reponse',TextareaType::class)
            ->add('prix')
            ->add('etat',ChoiceType::class,['choices'=>['Validé'=>true,'Non validé'=>false],'expanded'=> true,'multiple'=>false])
            ->add('save',SubmitType::class,['label'=>'Envoyer reponse'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReponseDevis::class,
        ]);
    }
}

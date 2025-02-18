<?php

namespace App\Form;

use App\Entity\Marche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarcheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idUser')
            ->add('prodid')
            ->add('nomprod')
            ->add('prix')
            ->add('dateajout')
            ->add('quantite')
            ->add('image', FileType::class, [
                'label' => 'Votre Image (JPG, JPEG, PNG file)',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marche::class,
        ]);
    }
}

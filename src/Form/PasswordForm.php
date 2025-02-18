<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Ajout de SubmitType

class PasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordConstraints = [
            new NotBlank([
                'message' => 'Veuillez entrer un mot de passe.',
            ]),
            new Length([
                'min' => 8,
                'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                'max' => 4096, // Longueur maximale recommandée par Symfony
            ]),
            new Regex([
                'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            ]),
            new NotCompromisedPassword([
                'message' => 'Ce mot de passe a été compromis dans une fuite de données. Veuillez en choisir un autre.',
            ]),
        ];

        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => $passwordConstraints,
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'class' => 'form-control', // Ajouter des classes CSS si nécessaire
                    ],
                ],
                'second_options' => [
                    'constraints' => $passwordConstraints,
                    'label' => 'Répétez le mot de passe',
                    'attr' => [
                        'class' => 'form-control', // Ajouter des classes CSS si nécessaire
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [ // Ajout du bouton de soumission
                'label' => 'Changer le mot de passe',
                'attr' => [
                    'class' => 'btn btn-warning mt-2 mb-2 form-control', // Ajouter des classes CSS si nécessaire
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
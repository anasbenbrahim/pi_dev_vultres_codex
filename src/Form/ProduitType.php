<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints as Assert;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomprod', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Entrez le nom du produit'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom du produit est obligatoire.']),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom du produit ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
                'attr' => ['placeholder' => 'Entrez le prix'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix est obligatoire.']),
                    new Assert\Positive(['message' => 'Le prix doit être un nombre positif.']),
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['placeholder' => 'Entrez la quantité'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\PositiveOrZero(['message' => 'La quantité ne peut pas être négative.']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false, // Not mapped to the entity (handle manually in controller)
                'required' => false,
                'attr' => ['accept' => 'image/*'],
                'constraints' => [
                    new Assert\Image([
                        'maxSize' => '2M',
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG, GIF).',
                    ]),
                ],
            ])
            ->add('descr', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Entrez une description du produit'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire.']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Produit disponible',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La catégorie est obligatoire.']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}

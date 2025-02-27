<?php

namespace App\Form;

use App\Entity\Objet;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ObjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'objet',
                'attr' => [
                    'placeholder' => 'Entrez le nom de l\'objet',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre objet (minimum 10 caractères)',
                    'class' => 'form-control',
                    'rows' => 4
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'objet',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG ou WEBP)',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 5 Mo'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png,image/webp'
                ]
            ])
            ->add('dateAjout', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'label' => 'Date d\'ajout',
                'attr' => [
                    'class' => 'form-control',
                    'readonly' => true
                ]
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Électronique' => 'electronique',
                    'Vêtements' => 'vetements',
                    'Livres' => 'livres',
                    'Sports & Loisirs' => 'sports_loisirs',
                    'Maison & Jardin' => 'maison_jardin',
                    'Autres' => 'autres'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;

        if (!$options['is_front']) {
            $builder->add('etat', ChoiceType::class, [
                'label' => 'État de l\'objet',
                'choices' => [
                    'Disponible' => 'disponible',
                    'En attente' => 'attendu'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'data' => 'disponible', // Default value
                'required' => true, // Make the field required
                'empty_data' => 'disponible', // Fallback value if null is submitted
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objet::class,
            'is_front' => false
        ]);

        $resolver->setAllowedTypes('is_front', 'bool');
    }
}

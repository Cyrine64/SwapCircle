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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ObjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Disponible' => 'disponible',
                    'Attendu' => 'attendu',
                ],
                'expanded' => true, // Render as radio buttons
                'multiple' => false, // Only one option can be selected
            ])
            ->add('date_ajout', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(), // Set default value to current date
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPEG, PNG file)',
                'mapped' => false, // Do not map this field directly to the entity
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG or PNG).',
                    ]),
                ],
            ])
            ->add('categorie', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objet::class,
        ]);
    }
}

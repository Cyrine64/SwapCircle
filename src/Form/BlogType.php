<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType; // Correct namespace
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('contenu', TextareaType::class)
            ->add('date_publication', DateTimeType::class, [
                'widget' => 'single_text', // Assurez-vous d'utiliser le bon format pour la date
            ])
            ->add('imageFile', FileType::class, [
                'required' => false, // L'utilisateur n'a pas à télécharger une nouvelle image
                'mapped' => false,   // La propriété imageFile ne correspond pas directement à une colonne dans la base de données
                'label' => 'Image de l\'article (si changement)',
                'attr' => ['accept' => 'image/*']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class, // L'entité Blog sera liée au formulaire
        ]);
    }
}

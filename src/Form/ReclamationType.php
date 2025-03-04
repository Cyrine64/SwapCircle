<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use App\Entity\TypeReclamation;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('type_reclamation', EnumType::class, [
                'class' => TypeReclamation::class,
                'choice_label' => fn ($choice) => match ($choice) {
                    TypeReclamation::Technique => 'Problème Technique',
                    TypeReclamation::Utilisateur => 'Problème Utilisateur',
                    TypeReclamation::Objet => 'Problème Objet',
                    TypeReclamation::Echange => 'Problème Échange',
                    TypeReclamation::Recyclage => 'Problème Recyclage',
                },
                'expanded' => false, // Utiliser true pour des boutons radio
                'multiple' => false, // true si plusieurs sélections autorisées
            ])
            
           
            ->add('titre')
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}

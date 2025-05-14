<?php

namespace App\Form;

use App\Entity\Recyclage;
use App\Entity\Objet;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecyclageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', EntityType::class, [
                'class' => Objet::class,
                'choice_label' => 'nom',
                'placeholder' => '-- Sélectionner un objet --',
                'label' => 'Objet :',
                'required' => true,
            ])
            ->add('type_recyclage', ChoiceType::class, [
                'choices' => [
                    'Plastique' => 'Plastique',
                    'Verre' => 'Verre',
                    'Papier' => 'Papier',
                    'Métal' => 'Métal',
                    'Organique' => 'Organique',
                ],
                'placeholder' => '-- Sélectionner un type --',
                'label' => 'Type de Recyclage :',
                'required' => true,
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire :',
                'required' => false,
            ])
            ->add('date_recyclage', DateTimeType::class, [
                'label' => 'Date de Recyclage :',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => '-- Sélectionner un utilisateur --',
                'label' => 'Utilisateur :',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recyclage::class,
        ]);
    }
} 
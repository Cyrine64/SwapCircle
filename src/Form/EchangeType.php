<?php

namespace App\Form;

use App\Entity\Echange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EchangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameEchange', TextType::class, [
                'label' => 'Titre de l\'échange',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Donnez un titre à votre proposition d\'échange',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre proposition',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Détaillez votre proposition d\'échange...',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ])
            ->add('dateEchange', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'label' => 'Date de la proposition',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en_attente',
                    'Accepté' => 'accepte',
                    'Refusé' => 'refuse'
                ],
                'required' => true,
                'data' => 'en_attente',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Echange::class,
        ]);
    }
}

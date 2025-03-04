<?php

namespace App\Form;

use App\Entity\Tutorial;
use App\Entity\Recyclage;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutorialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('vid_URL', TextType::class, [
                'label' => 'URL de la vidéo',
            ])
            ->add('date_creation', DateTimeType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
            ])
            ->add('recyclage', EntityType::class, [
                'class' => Recyclage::class,
                'choice_label' => 'type_recyclage',
                'label' => 'Recyclage lié',
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',
                'label' => 'Créé par',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter le Tutoriel',
                'attr' => ['class' => 'bg-blue-500 text-red px-4 py-2 rounded']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutorial::class,
        ]);
    }
}

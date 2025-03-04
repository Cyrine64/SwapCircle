<?php

namespace App\Form;

use App\Entity\Echange;
use App\Entity\Objet;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class EchangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $objetPropose = $options['objet_propose'];
        $user = $options['user'];

        $builder
            ->add('nameEchange', TextType::class, [
                'label' => 'Titre de l\'échange',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Donnez un titre à votre proposition d\'échange'
                ]
            ])
            ->add('objet', EntityType::class, [
                'class' => Objet::class,
                'choice_label' => 'nom',
                'label' => 'Votre objet à échanger',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('o')
                        ->where('o.id_utilisateur = :user')
                        ->andWhere('o.etat = :etat')
                        ->setParameter('user', $user)
                        ->setParameter('etat', 'disponible');
                },
                'placeholder' => 'Choisissez un de vos objets à échanger',
                'required' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message pour le propriétaire',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Expliquez pourquoi vous souhaitez faire cet échange...',
                    'rows' => 4
                ]
            ])
            ->add('dateEchange', DateTimeType::class, [
                'label' => 'Date souhaitée pour l\'échange',
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
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
                'data' => 'en_attente'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['objet_propose', 'user']);
        $resolver->setAllowedTypes('objet_propose', Objet::class);
        $resolver->setAllowedTypes('user', Utilisateur::class);
        
        $resolver->setDefaults([
            'data_class' => Echange::class,
        ]);
    }
}

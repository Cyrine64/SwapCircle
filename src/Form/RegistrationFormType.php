<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(['message' => 'Name cannot be empty']),
                ],
            ])
            ->add('last_name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank(['message' => 'Last name cannot be empty']),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank(['message' => 'Email cannot be empty']),
                    new Email(['message' => 'Please enter a valid email']),
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Password',
                'constraints' => [
                    new NotBlank(['message' => 'Password cannot be empty']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                    ]),
                ],
                'mapped' => true, // Ensures it's saved in the entity
            ])
            ->add('role', HiddenType::class, [
                'data' => 'ROLE_USER', // Default role for new users
                'mapped' => false, // Prevents this from being directly mapped to the entity
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Enter the CAPTCHA',
                'mapped' => false,

                'constraints' => [new NotBlank(['message' => 'Please enter the CAPTCHA'])],
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'registration',
        ]);
    }
}

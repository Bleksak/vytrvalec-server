<?php

namespace App\Form;

use App\Dto\UserDto;
use App\Entity\Faculty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                    new Assert\Email(message: 'invalid'),
                ]
            ])
            ->add('first_name', TextType::class, [
                'property_path' => 'firstName',
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                ]
            ])
            ->add('last_name', TextType::class, [
                'property_path' => 'lastName',
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                    new Assert\PasswordStrength(message: 'weak', minScore: 1),
                ]
            ])
            ->add('faculty', EntityType::class, [
                'class' => Faculty::class,
                'choice_filter' => 'visible',
                'required' => true,
                'invalid_message' => 'invalid',
                'constraints' => [
                    new Assert\NotBlank(message: 'invalid', allowNull: false),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', UserDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

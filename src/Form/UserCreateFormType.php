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
                    new Assert\NotBlank(message: 'blank_email'),
                    new Assert\NotNull(message: 'blank_email'),
                    new Assert\Email(message: 'bad_email'),
                ]
            ])
            ->add('first_name', TextType::class, [
                'property_path' => 'firstName',
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_first_name'),
                    new Assert\NotNull(message: 'blank_first_name'),
                ]
            ])
            ->add('last_name', TextType::class, [
                'property_path' => 'lastName',
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_last_name'),
                    new Assert\NotNull(message: 'blank_last_name'),
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_password', allowNull: false),
                    new Assert\NotNull(message: 'blank_password'),
                    new Assert\PasswordStrength(message: 'weak_password', minScore: 2),
                ]
            ])
            ->add('faculty', EntityType::class, [
                'class' => Faculty::class,
                'choice_filter' => 'visible',
                'required' => true,
                'invalid_message' => 'invalid_faculty',
                'constraints' => [
                    new Assert\NotBlank(message: 'invalid_faculty', allowNull: false),
                    new Assert\NotNull(message: 'invalid_faculty'),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', UserDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

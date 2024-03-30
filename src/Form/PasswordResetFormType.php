<?php

namespace App\Form;

use App\Dto\PasswordResetDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                    new Assert\PasswordStrength(message: 'weak', minScore: 1),
                ]
            ])
            ->add('password_reset_token', TextType::class, [
                'property_path' => 'passwordResetToken',
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', PasswordResetDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

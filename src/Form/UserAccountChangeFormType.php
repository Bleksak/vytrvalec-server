<?php

namespace App\Form;

use App\Dto\UserAccountChangeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class UserAccountChangeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('old_password', PasswordType::class, [
            'required' => true,
            'property_path' => 'oldPassword',
            'constraints' => [
                new Assert\NotBlank(message: 'blank', allowNull: false),
            ],
        ]);
        $builder->add('password', PasswordType::class, [
            'property_path' => 'password',
            'constraints' => [
                new Assert\PasswordStrength(message: 'weak', minScore: 1),
            ],
        ]);
        $builder->add('email', EmailType::class, [
            'property_path' => 'email',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', UserAccountChangeDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

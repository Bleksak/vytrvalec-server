<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\User\ForgottenPasswordResetDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ForgottenPasswordResetType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_name' => 'password',
            'second_name' => 'password_repeat',
            'first_options' => [
                'label' => 'user.forgotten_password.password',
                'always_empty' => false,
            ],
            'second_options' => [
                'label' => 'user.forgotten_password.password_repeat',
                'always_empty' => false,
            ],
        ]);

        $builder->add('password_reset_token', HiddenType::class, [
            'property_path' => 'passwordResetToken',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'user.forgotten_password.change',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForgottenPasswordResetDto::class,
        ]);
    }
}

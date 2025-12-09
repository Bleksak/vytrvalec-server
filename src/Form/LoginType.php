<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\User\UserLoginDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LoginType extends AbstractType
{
    #[\Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->add('email', EmailType::class, [
            'label' => 'login.email',
        ]);
        $builder->add('password', PasswordType::class, [
            'label' => 'login.password',
            'always_empty' => false,
        ]);
        $builder->add('submit', SubmitType::class, [
            'label' => 'login.submit',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', UserLoginDto::class);
    }
}

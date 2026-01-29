<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\User\ForgottenPasswordRequestDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ForgottenPasswordRequestType extends AbstractType
{
    #[\Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->add('email', EmailType::class, [
            'label' => 'user.forgotten_password.email',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'user.forgotten_password.submit',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ForgottenPasswordRequestDto::class);
    }
}

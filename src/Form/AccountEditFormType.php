<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\User\AccountEditDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AccountEditFormType extends AbstractType
{
    #[\Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->add('mailing', CheckboxType::class, [
            'label' => 'account.edit.mailing',
            'required' => false,
            'attr' => [
                'role' => 'switch',
            ],
        ]);

        $builder->add('current_password', PasswordType::class, [
            'label' => 'account.edit.current_password',
            'always_empty' => false,
            'property_path' => 'currentPassword',
            'required' => false,
        ]);

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => false,
            'first_name' => 'new_password',
            'second_name' => 'new_password_repeat',
            'property_path' => 'newPassword',
            'first_options' => [
                'label' => 'account.edit.new_password',
                'always_empty' => false,
            ],
            'second_options' => [
                'label' => 'account.edit.new_password_repeat',
                'always_empty' => false,
            ],
        ]);

        $builder->add('anonymize', CheckboxType::class, [
            'label' => 'account.edit.anonymize',
            'attr' => [
                'role' => 'switch',
            ],
            'required' => false,
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'account.edit.submit',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', AccountEditDto::class);
    }
}

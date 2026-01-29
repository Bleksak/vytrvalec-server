<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AccountDeleteFormType extends AbstractType
{
    #[\Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        $builder->setMethod(Request::METHOD_DELETE);

        $builder->add('submit', SubmitType::class, [
            'label' => 'account.delete.submit',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}

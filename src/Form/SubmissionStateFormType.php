<?php

namespace App\Form;

use App\Dto\SubmissionStateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SubmissionStateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('updated_at', DateTimeType::class, [
            'required' => true,
            'property_path' => 'updatedAt',
            'constraints' => [
                new Assert\NotBlank(message: 'blank_updated_at'),
                new Assert\NotNull(message: 'blank_updated_at'),
            ],
        ]);

        $builder->add('state', CheckboxType::class, [
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(message: 'blank_state'),
                new Assert\NotNull(message: 'blank_state'),
            ],
        ]);

        $builder->add('message', CheckboxType::class, [
            'required' => false,
            'empty_data' => '',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SubmissionStateDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

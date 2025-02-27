<?php

namespace App\Form;

use App\Dto\CharityDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class CharityCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $method = $options['method'] ?? 'POST';
        $required = match ($method) {
            'POST', 'PUT' => true,
            'PATCH' => false,
            default => true,
        };

        $builder->add('name', TextType::class, [
            'required' => $required,
            'constraints' => [
                new Assert\NotBlank(message: 'blank_name'),
                new Assert\NotNull(message: 'blank_name'),
            ],
        ]);

        $builder->add('description', TextareaType::class, [
            'required' => $required,
            'constraints' => [
                new Assert\NotBlank(message: 'blank_description'),
                new Assert\NotNull(message: 'blank_description'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CharityDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

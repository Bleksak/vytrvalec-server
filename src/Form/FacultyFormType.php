<?php

namespace App\Form;

use App\Dto\FacultyDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FacultyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $method = $options['method'] ?? 'POST';
        $required = match ($method) {
            'POST', 'PUT' => true,
            'PATCH' => false,
        };
        
        $builder->add('name', TextType::class, [
            'required' => $required,
            'constraints' => [
                new Assert\NotNull(),
                new Assert\NotBlank(),
            ]
        ]);

        $builder->add('shortcut', TextType::class, [
            'required' => $required,
            'constraints' => [
                new Assert\NotNull(),
                new Assert\NotBlank(),
            ]
        ]);

        $builder->add('visible', CheckboxType::class, [
            'required' => false,
            'empty_data' => match ($method) {
                'POST', 'PUT' => true,
                'PATCH' => null,
                default => false,
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', FacultyDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

<?php

namespace App\Form;

use App\Dto\ActivityDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ActivityFormType extends AbstractType
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
                new Assert\NotBlank(message: 'blank_name', allowNull: false),
            ]
        ]);

        $builder->add('min_elevation', IntegerType::class, [
            'required' => $required,
            'property_path' => 'minElevation',
            'constraints' => [
                new Assert\NotBlank(message: 'blank_min_elevation', allowNull: false),
                new Assert\GreaterThanOrEqual(0, message: 'negative_min_elevation'),
                new Assert\Type(type: IntegerType::class, message: 'invalid_min_elevation'),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ActivityDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

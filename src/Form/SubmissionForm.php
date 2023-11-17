<?php

namespace App\Form;

use App\Dto\SubmissionDto;
use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SubmissionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $method = $options['method'] ?? 'POST';

        $required = match ($method) {
            'POST' => true,
            'PATCH' => false
        };

        $builder->add('elevation', IntegerType::class, [
            'required' => false,
            'constraints' => [
                new Assert\GreaterThanOrEqual(0, message: 'negative_elevation'),
            ],
        ]);

        $builder->add('distance', IntegerType::class, [
            'required' => $required,
            'constraints' => ($required ? [
                new Assert\NotBlank(message: 'blank_distance'),
                new Assert\NotNull(message: 'blank_distance'),
            ] : []) + [
                new Assert\GreaterThanOrEqual(0, message: 'negative_distance'),
            ],
        ]);

        $builder->add('image', FileType::class, [
            'required' => $required,
            'constraints' => ($required ? [
                new Assert\NotBlank(message: 'blank_image'),
                new Assert\NotNull(message: 'blank_image'),
            ] : []) + [
                new Assert\Image(mimeTypesMessage: 'bad_image'),
            ],
        ]);

        $builder->add('activity', EntityType::class, [
            'required' => $required,
            'class' => Activity::class,
            'choice_label' => 'name',
            'invalid_message' => 'invalid_activity',

            'constraints' => ($required ? [
                new Assert\NotBlank(message: 'blank_activity'),
                new Assert\NotNull(message: 'blank_activity'),
            ] : []),
        ]);

        $builder->add('updated_at', DateTimeType::class, [
            'required' => $method === 'PATCH',
            'property_path' => 'updatedAt',
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
            'constraints' => ($method === 'PATCH' ? [
                new Assert\NotBlank(message: 'blank_updated_at'),
                new Assert\NotNull(message: 'blank_updated_at'),
            ] : []),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SubmissionDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

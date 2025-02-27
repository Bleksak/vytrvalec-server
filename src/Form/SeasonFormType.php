<?php

namespace App\Form;

use App\Dto\SeasonDto;
use App\Entity\Charity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class SeasonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $method = $options['method'] ?? 'POST';

        $required = match ($method) {
            'POST', 'PUT' => true,
            'PATCH' => false,
            default => true,
        };

        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $builder->add('start', DateType::class, [
            'required' => $required,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',

            'constraints' => ($required ? [
                new Assert\NotBlank(message: 'blank_start'),
                new Assert\NotNull(message: 'blank_start'),
            ] : []) + [
                new Assert\GreaterThanOrEqual($now),
            ],
        ]);
        $builder->add('end', DateType::class, [
            'required' => $required,
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',

            'constraints' => ($required ? [
                new Assert\NotBlank(message: 'blank_end'),
                new Assert\NotNull(message: 'blank_end'),
            ] : []) + [
                new Assert\GreaterThan(propertyPath: 'parent.all[start].data'),
            ],
        ]);

        $builder->add('charity', EntityType::class, [
            'required' => $required,
            'class' => Charity::class,
            'choice_label' => 'name',
            'invalid_message' => 'invalid_charity',

            'constraints' => ($required ? [
                new Assert\NotBlank(message: 'blank_charity'),
                new Assert\NotNull(message: 'blank_charity'),
            ] : []),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SeasonDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

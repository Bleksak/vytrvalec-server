<?php

namespace App\Form;

use App\Dto\SeasonDto;
use App\Entity\Charity;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SeasonCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $now = new DateTimeImmutable();

        $builder->add('start', DateType::class, [
            'constraints' => [
                new Assert\GreaterThanOrEqual($now)
            ]
        ]);
        $builder->add('end', DateType::class, [
            'constraints' => [
                new Assert\GreaterThan(propertyPath: 'parent.all[start].data')
            ]
        ]);

        $builder->add('charity', EntityType::class, [
            'class' => Charity::class,
            'choice_label' => 'name',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SeasonDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

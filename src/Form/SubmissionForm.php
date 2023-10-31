<?php

namespace App\Form;

use App\Dto\SubmissionDto;
use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SubmissionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    	$builder->add('elevation', IntegerType::class, [
            'required' => false,
            'constraints' => [
                new Assert\GreaterThanOrEqual(0)
            ],
        ]);

    	$builder->add('distance', IntegerType::class, [
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(allowNull: false),
                new Assert\GreaterThanOrEqual(0)
            ],
        ]);

    	$builder->add('image', FileType::class, [
            'required' => true,
            'constraints' => [
                new Assert\NotNull(),
                new Assert\Image(),
            ],
        ]);

        $builder->add('activity', EntityType::class, [
            'required' => true,
            'class' => Activity::class,
            'choice_label' => 'name',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SubmissionDto::class);
    	$resolver->setDefault('csrf_protection', false);
    }
}

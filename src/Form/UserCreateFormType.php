<?php

namespace App\Form;

use App\Dto\UserCreateDTO;
use App\Entity\Faculty;
use App\Requests\UserCreateRequest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_email'),
                    new Assert\Email(message: 'bad_email'),
                ]
            ])
            ->add('first_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_first_name'),
                ]
            ])
            ->add('last_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_last_name'),
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank_password'),
                    new Assert\NotNull(message: 'blank_password'),
                    // TODO: Uncomment this and configure the strength
                    // new Assert\PasswordStrength(message: 'weak_password'),
                ]
            ])
            ->add('faculty', EntityType::class, [
                'class' => Faculty::class,
                'choice_filter' => 'visible',
                'required' => true,
                'invalid_message' => 'invalid_faculty',
                'constraints' => [
                    new Assert\NotBlank(message: 'invalid_faculty'),
                    new Assert\NotNull(message: 'invalid_faculty'),
                ]
            ])
            ->setMethod('POST')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity(['message' => 'not_unique_email', 'fields' => 'email']),
            ],
            'data_class' => UserCreateDto::class,
        ]);
    }
}

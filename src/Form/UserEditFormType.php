<?php

namespace App\Form;

use App\Dto\UserEditDto;
use App\Entity\Faculty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\Email(message: 'invalid'),
                ]
            ])
            ->add('first_name', TextType::class, [
                'property_path' => 'firstName',
            ])
            ->add('last_name', TextType::class, [
                'property_path' => 'lastName',
            ])
            ->add('faculty', EntityType::class, [
                'class' => Faculty::class,
                'choice_filter' => 'visible',
                'required' => true,
                'invalid_message' => 'invalid',
            ])
            ->add('banned', HiddenType::class, [
                'property_path' => 'banned',
            ])
            ->add('roles', CollectionType::class, [
                'property_path' => 'roles',
                'entry_type' => TextType::class,
                'allow_add' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', UserEditDto::class);
        $resolver->setDefault('csrf_protection', false);
    }
}

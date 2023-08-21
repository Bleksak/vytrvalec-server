<?php

namespace App\Form;

use App\Entity\Faculty;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'email',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank(null, 'email_empty_error'),
                    new Assert\Email(null, 'email_format_error'),
                    new Assert\Type('string', 'email_format_error'),
                ]
            ])
            ->add('first_name', TextType::class, [
                'label' => 'first_name',
                'attr' => [
                    'class' => 'form-control d-inline'
                ],
                'constraints' => [
                    new Assert\NotBlank(null, 'first_name_empty_error')
                ]
            ])
            ->add('last_name', TextType::class, [
                'label' => 'last_name',
                'attr' => [
                    'class' => 'form-control d-inline'
                ],
                'constraints' => [
                    new Assert\NotBlank(null, 'last_name_empty_error')
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'passwords_do_not_match',
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],

                'options' => [
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('faculty', EntityType::class, [
                'label' => 'faculty',

                'class' => Faculty::class,
                'choice_filter' => 'visible',
                'choice_label' => 'shortcut',
                'required' => true,
                'invalid_message' => 'invalid_faculty_selected',
                'placeholder' => 'empty_field',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('gdpr', CheckboxType::class, [
                'label' => 'gdpr_label',
                'attr' => [
                    'class' => 'form-check-input border border-2 border-danger',
                ],
                'label_attr' => [
                    'id' => 'gdpr',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $this->translator->trans('gdpr_tooltip'),
                ],

                'mapped' => false,
                'constraints' => [
                    new Assert\Required(),
                    new Assert\EqualTo(true, null, 'gdpr_must_be_true')
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'sign_up',
                'attr' => [
                    'class' => 'btn btn-primary d-block mx-1 my-1'
                ]
            ])
            ->setMethod('POST')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                // new Callback([$this, 'validatePasswordMatch'])
                new UniqueEntity(['message' => 'email_not_unique', 'fields' => 'email']),
            ],
            'data_class' => User::class,
        ]);
    }
}

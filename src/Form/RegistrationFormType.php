<?php

namespace App\Form;

use App\Entity\Faculty;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    private $translator;

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
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ])
            ->add('first_name', TextType::class, [
                'label' => 'first_name',
                'attr' => [
                    'class' => 'form-control d-inline'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('last_name', TextType::class, [
                'label' => 'last_name',
                'attr' => [
                    'class' => 'form-control d-inline'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'password',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('password_repeat', PasswordType::class, [
                'label' => 'password_repeat',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('faculty', EntityType::class, [
                'label' => 'faculty',

                'class' => Faculty::class,
                'choice_filter' => 'visible',
                'choice_label' => 'shortcut',
                'required' => true,
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
                'constraints' => [
                    new Assert\Required(),
                    // TODO: what is property path? etc.. make this work
                    new Assert\EqualTo(true)
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
                new Callback([$this, 'validatePasswordMatch'])
            ]
            // 'data_class' => User::class,
        ]);
    }

    public function validatePasswordMatch($data, ExecutionContextInterface $context)
    {
        $password = $data['password'];
        $password_repeat = $data['password_repeat'];

        if($password !== $password_repeat) {
            $context->buildViolation('passwords_do_not_match')
            ->atPath('password_repeat')
            ->addViolation();
        }
    }
}

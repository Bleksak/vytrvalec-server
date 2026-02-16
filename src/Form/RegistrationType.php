<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\UserRegistrationDto;
use App\Entity\Faculty;
use App\Form\DataTransformers\FacultyEntityToIdDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegistrationType extends AbstractType
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher,
        private TranslatorInterface $translator,
    ) {}

    #[\Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        /** @var array<int, Faculty> */
        $faculties = $options['faculties'] ?? [];

        $builder->add('faculty', EntityType::class, [
            'class' => Faculty::class,
            'choices' => $faculties,
            'label' => 'registration.faculty',
            'choice_label' =>
                fn(Faculty $faculty): ?string => $faculty->translations->get(
                    $this->localeSwitcher->getLocale(),
                )?->name,
        ]);

        $builder->add('email', EmailType::class, [
            'label' => 'registration.email',
        ]);

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_name' => 'password',
            'second_name' => 'password_repeat',
            'first_options' => [
                'label' => 'registration.password',
                'always_empty' => false,
            ],
            'second_options' => [
                'label' => 'registration.password_repeat',
                'always_empty' => false,
            ],
        ]);

        $builder->add('first_name', TextType::class, [
            'label' => 'registration.first_name',
            'property_path' => 'firstName',
        ]);

        $builder->add('last_name', TextType::class, [
            'label' => 'registration.last_name',
            'property_path' => 'lastName',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'registration.submit',
        ]);

        $builder->add('anonymize', CheckboxType::class, [
            'label' => 'registration.anonymize',
            'label_attr' => [
                'data-tooltip' => $this->translator->trans(
                    'registration.anonymize_tooltip',
                ),
            ],
        ]);
        $builder->add('gdpr', CheckboxType::class, [
            'label' => 'registration.gdpr',
            'label_attr' => [
                'data-tooltip' => $this->translator->trans(
                    'registration.gdpr_tooltip',
                ),
            ],
            'property_path' => 'gdpr',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'registration.submit',
        ]);

        $builder->get('faculty')->addModelTransformer(
            new FacultyEntityToIdDataTransformer($faculties),
        );
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('faculties');
        $resolver->setDefault('data_class', UserRegistrationDto::class);
    }
}

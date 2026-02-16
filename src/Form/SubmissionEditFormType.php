<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\Submission\SubmissionServerEditDto;
use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Component\Uid\Uuid;

final class SubmissionEditFormType extends AbstractType
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher,
    ) {}

    #[\Override]
    public function buildForm(
        FormBuilderInterface $builder,
        array $options,
    ): void {
        /** @var array<int, Activity> */
        $activities = $options['activities'] ?? [];

        $builder->add('distance', IntegerType::class, [
            'label' => 'submission.edit.distance',
            'required' => true,
        ]);

        $builder->add('elevation', IntegerType::class, [
            'label' => 'submission.edit.elevation',
            'required' => true,
        ]);

        $builder->add('image_uuid', HiddenType::class, [
            'label' => 'submission.edit.image',
            'property_path' => 'imageUuid',
        ]);

        $builder->add('activity', EntityType::class, [
            'class' => Activity::class,
            'choices' => $activities,
            'label' => 'submission.edit.activity',
            'choice_label' =>
                fn(Activity $faculty): ?string => $faculty->translations->get(
                    $this->localeSwitcher->getLocale(),
                )?->name,
        ]);

        $builder->add('updated_at', HiddenType::class, [
            'property_path' => 'updatedAt',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'submission.edit.submit',
        ]);

        $builder->get('image_uuid')->addModelTransformer(
            new CallbackTransformer(
                static fn(?Uuid $value): ?string => $value?->toString(),
                static fn(?string $value): ?Uuid => $value
                    ? Uuid::fromString($value)
                    : null,
            ),
        );

        $builder->get('updated_at')->addModelTransformer(
            new CallbackTransformer(
                static fn(?\DateTime $value): ?string => $value?->format(\DateTimeInterface::ATOM),
                static fn(?string $value): ?\DateTime => $value
                    ? new \DateTime($value)
                    : null,
            ),
        );
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SubmissionServerEditDto::class);
        $resolver->setDefined('activities');
    }
}

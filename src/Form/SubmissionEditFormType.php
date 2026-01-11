<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\Submission\SubmissionServerEditDto;
use App\Entity\Activity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\LocaleSwitcher;

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
                fn(Activity $faculty): ?string => $faculty->translations->get($this->localeSwitcher->getLocale())?->name,
        ]);

        $builder->add('updated_at', HiddenType::class, [
            'property_path' => 'updatedAt',
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'submission.edit.submit',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SubmissionServerEditDto::class);
        $resolver->setDefined('activities');
    }
}

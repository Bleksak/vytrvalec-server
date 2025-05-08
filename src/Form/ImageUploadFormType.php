<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\Image\ImageUploadDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<mixed>
 */
final class ImageUploadFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'blank', allowNull: false),
                    new Assert\Image(mimeTypesMessage: 'invalid', maxSize: '15M', maxSizeMessage: 'too_large'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImageUploadDto::class,
            'csrf_protection' => false,
        ]);
    }
}

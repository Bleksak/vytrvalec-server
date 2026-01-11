<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Services\ImageUploader;
use App\Utils\MimeType;
use App\Utils\Toast\ToastContext;
use App\Utils\Toast\ToastManager;
use App\Utils\Toast\ToastType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ImageUpload extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public ?string $image = null;

    /** @var list<string> */
    #[LiveProp]
    public array $allowedMimeTypes = [];

    #[LiveProp]
    public bool $disabled = false;

    public function __construct(
        private readonly ImageUploader $imageUploader,
        private readonly ToastManager $toastManager,
    ) {}

    /**
     * @param list<string> $allowedMimeTypes
     */
    public function mount(
        ?string $image = null,
        bool $disabled = false,
        array $allowedMimeTypes = [],
    ): void {
        $this->image = $image;
        $this->disabled = $disabled;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    #[LiveListener('updateImage')]
    public function updateImage(#[LiveArg('image')] ?string $image): void
    {
        $this->image = $image;
    }

    #[LiveAction]
    public function submit(Request $request): void
    {
        /** @var UploadedFile|null */
        $uploadedImage = $request->files->get('image', null);

        if ($uploadedImage === null) {
            $this->toastManager->add(
                ToastType::Error,
                ToastContext::ImageUpload,
                message: 'image_upload.error',
            );

            return;
        }

        \assert($uploadedImage instanceof UploadedFile);

        // TODO(@bleksak): validate uploaded image

        $allowedMimeTypes = \array_map(
            MimeType::from(...),
            $this->allowedMimeTypes,
        );

        $image = $this->imageUploader->uploadImage(
            $uploadedImage,
            $allowedMimeTypes,
        );

        if ($image === null) {
            $this->toastManager->add(
                ToastType::Error,
                ToastContext::ImageUpload,
                message: 'image_upload.error',
            );

            return;
        }

        $this->image = $image->path;

        $this->emitUp('image-upload', ['uuid' => $image->uuid]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Dto\Image\ImageUploadDto;
use App\Dto\Image\Response\ImageCreateResponseDto;
use App\Form\ImageUploadFormType;
use App\Services\ImageUploader;
use App\Validation\FormErrors;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Image')]
final class ImageController extends AbstractController
{
    public function __construct(
        private readonly ImageUploader $imageUploader,
    ) {
    }

    #[OA\Post(
        description: 'Upload an image to the server',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'file',
                schema: new OA\Schema(ref: new Model(type: ImageUploadDto::class)),
            )
        ),
        responses: [
            new OA\Response(
                description: 'Image uploaded successfully',
                response: Response::HTTP_OK,
                content: new OA\JsonContent(
                    ref: new Model(type: ImageCreateResponseDto::class),
                ),
            ),
        ]
    )]
    #[Route('/api/image', 'image_store', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function store(Request $request): Response
    {
        $images = $request->files->all();

        $form = $this->createForm(ImageUploadFormType::class);

        $form->submit($images);

        $errors = FormErrors::collect($form);

        if (count($errors) !== 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        // @phpstan-ignore-next-line
        $image = $this->imageUploader->uploadImage($form->getData()->image);

        if ($image === null) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            new ImageCreateResponseDto(
                $image->getUuid(),
                $image->getPath(),
                $image->getUploadedAt(),
                $image->getUsedAt()
            )
        );
    }
}

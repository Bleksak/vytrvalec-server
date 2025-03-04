<?php

namespace App\Controller\ApiResource;

use App\Action\CharityActions;
use App\Dto\CharityDto;
use App\Entity\Charity;
use App\Form\CharityCreateFormType;
use App\Repository\CharityRepository;
use App\Validation\FormErrors;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OA\Tag(name: 'Charity')]
final class CharityController extends AbstractController
{
    public function __construct(
        private readonly CharityActions $action,
        private readonly CharityRepository $charityRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[OA\Post(
        description: 'Create a new Charity',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'The new charity object',
            content: new OA\JsonContent(
                ref: new Model(type: CharityDto::class)
            ),
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Charity created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'id',
                            type: 'integer',
                            example: 1,
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Unauthorized access',
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Bad data',
            ),
        ]
    )]
    #[Route(
        '/api/charity',
        'api_charity_create',
        methods: ['POST'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(CharityCreateFormType::class);
        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $id = $this->action->create($form->getData());

        return $this->json(['id' => $id], Response::HTTP_CREATED);
    }

    #[OA\Get(
        description: 'Retrieve a Charity',
        parameters: [
            new OA\Parameter(
                name: 'charity',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'OK',
                content: new OA\JsonContent(
                    ref: new Model(type: Charity::class)
                )
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Charity with the given ID not found',
            ),
        ]
    )]
    #[Route(
        '/api/charity/{charity}',
        'api_charity_get',
        methods: ['GET'],
    )]
    public function get(Charity $charity): Response
    {
        return $this->json($this->normalizer->normalize($charity, null, [
            AbstractNormalizer::GROUPS => ['fetchCharity'],
        ]));
    }

    #[OA\Patch(
        description: 'Update a charity',
        parameters: [
            new OA\Parameter(
                name: 'charity',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                ref: new Model(type: CharityDto::class)
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Succesfully updated',
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: 'Charity with given ID not found',
            ),
        ]
    )]
    #[Route(
        '/api/charity/{charity}',
        'api_charity_patch',
        methods: ['PATCH'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function updatePatch(Charity $charity, Request $request): Response
    {
        $form = $this->createForm(CharityCreateFormType::class, null, [
            'method' => $request->getMethod(),
        ]);

        $form->submit($request->getPayload()->all());

        $errors = FormErrors::collect($form);

        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->action->update($charity, $form->getData());

        return new Response();
    }

    #[OA\Get(
        description: 'Retrieve a collection of charities',
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Collection of charities',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: Charity::class),
                    ),
                ),
            ),
        ],
    )]
    #[Route(
        '/api/charity',
        'api_charity_index',
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->json($this->normalizer->normalize($this->charityRepository->findAll(), null, [
            AbstractNormalizer::GROUPS => ['fetchCharity'],
        ]));
    }

    #[OA\Delete(
        description: 'Delete the given Charity',
        parameters: [
            new OA\Parameter(
                name: 'charity',
                in: 'path',
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Succesfully deleted given Charity',
            ),
        ],
    )]
    #[Route(
        '/api/charity/{charity}',
        'api_charity_delete',
        methods: ['DELETE'],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function delete(Charity $charity): Response
    {
        $this->charityRepository->remove($charity, true);

        return new Response(status: Response::HTTP_OK);
    }
}

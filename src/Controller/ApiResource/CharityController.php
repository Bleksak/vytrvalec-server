<?php

declare(strict_types=1);

namespace App\Controller\ApiResource;

use App\Action\CharityActions;
use App\Dto\Charity\CharityCreateDto;
use App\Dto\Charity\CharityEditDto;
use App\Dto\Charity\Response\CharityCreateResponseDto;
use App\Dto\Charity\Response\CharityGetResponseDto;
use App\Dto\Charity\Response\CharityIndexResponseDto;
use App\Entity\Charity;
use App\Repository\CharityRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Charity')]
final class CharityController extends AbstractController
{
    public function __construct(
        private readonly CharityActions $action,
        private readonly CharityRepository $charityRepository,
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    #[OA\Post(
        description: 'Create a new Charity',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'The new charity object',
            content: new OA\JsonContent(
                ref: new Model(type: CharityCreateDto::class)
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
    public function create(
        #[MapRequestPayload]
        CharityCreateDto $charityCreateDto,
    ): Response {
        $charity = $this->action->create($charityCreateDto);

        if (is_array($charity)) {
            return $this->json($charity, Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            new CharityCreateResponseDto(
                $charity->getId(),
                $charity->getName(),
                $charity->getDescription(),
                $charity->getImage() === null ? null : $this->parameterBag->get('app_base').$charity->getImage()->getPath(),
                $charity->getWebsite(),
            ),
            Response::HTTP_CREATED
        );
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
    public function get(
        Charity $charity,
    ): Response {
        return $this->json(
            new CharityGetResponseDto(
                $charity->getId(),
                $charity->getName(),
                $charity->getDescription(),
                $charity->getImage() === null ? null : $this->parameterBag->get('app_base').$charity->getImage()->getPath(),
                $charity->getWebsite(),
            ),
        );
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
                ref: new Model(type: CharityEditDto::class)
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
    public function updatePatch(
        Charity $charity,
        #[MapRequestPayload]
        CharityEditDto $charityEditDto,
    ): Response {
        $this->action->update($charity, $charityEditDto);

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
    public function index(
        ParameterBagInterface $bag,
    ): Response {
        return $this->json(
            array_map(
                fn (Charity $charity) => new CharityIndexResponseDto(
                    $charity->getId(),
                    $charity->getName(),
                    $charity->getDescription(),
                    $charity->getImage() === null ? null : $this->parameterBag->get('app_base').$charity->getImage()->getPath(),
                    $charity->getWebsite(),
                ),
                $this->charityRepository->findAll(),
            )
        );
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

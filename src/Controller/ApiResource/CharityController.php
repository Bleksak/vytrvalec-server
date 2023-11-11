<?php

namespace App\Controller\ApiResource;

use App\Action\CharityActions;
use App\Attributes\ApiResource;
use App\Attributes\ApiRoute;
use App\Entity\Charity;
use App\Form\CharityCreateFormType;
use App\Repository\CharityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ApiResource('Charity')]
class CharityController extends AbstractController
{
    public function __construct(
        private readonly CharityActions $action,
        private readonly CharityRepository $charityRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[ApiRoute(
        '/api/charity',
        'api_charity_create',
        methods: ['POST'],
        documentation: 'Create a new Charity entity',
        responses: [
            Response::HTTP_BAD_REQUEST => ['message' => 'Bad data'],
            Response::HTTP_UNAUTHORIZED => ['message' => 'Unauthorized access'],
            Response::HTTP_CREATED => ['message' => 'Entity created'],
        ],
        requestScheme: [
            'name' => 'string',
            'description' => '?string',
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(CharityCreateFormType::class);
        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            $errors = [];

            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->action->create($form->getData());

        return $this->json([], Response::HTTP_CREATED);
    }

    #[ApiRoute(
        '/api/charity/{charity}',
        'api_charity_get',
        methods: ['GET'],
        documentation: 'Get a charity entity',
        responses: [
            Response::HTTP_OK => ['message' => 'Charity entity'],
        ],
    )]
    public function get(Charity $charity): Response
    {
        return $this->json($this->normalizer->normalize($charity, null, [
            AbstractNormalizer::GROUPS => ['fetchCharity'],
        ]));
    }


    #[ApiRoute(
        '/api/charity/{charity}',
        'api_charity_patch',
        methods: ['PATCH'],
        documentation: 'Patch a charity entity',
        responses: [
            Response::HTTP_OK => ['message' => 'Patched successfully'],
        ],
    )]
    #[IsGranted('ROLE_STAFF')]
    public function updatePatch(Charity $charity, Request $request): Response
    {
        $form = $this->createForm(CharityCreateFormType::class, null, [
            'method' => $request->getMethod()
        ]);

        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            $errors = [];

            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->action->update($charity, $form->getData());

        return $this->json([]);
    }

    #[ApiRoute(
        '/api/charity',
        'api_charity_index',
        methods: ['GET'],
        documentation: 'List of charity entities',
        responses: [
            Response::HTTP_OK => ['message' => 'List of charities'],
        ],
    )]
    public function index(): Response
    {
        return $this->json($this->normalizer->normalize($this->charityRepository->findAll(), null, [
            AbstractNormalizer::GROUPS => ['fetchCharity']
        ]));
    }
}

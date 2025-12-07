<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\UserActions;
use App\Dto\UserRegistrationDto;
use App\Form\RegistrationType;
use App\Repository\FacultyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    '/registration',
    name: self::ROUTE,
    methods: [Request::METHOD_GET, Request::METHOD_POST],
)]
final class RegistrationController extends AbstractController
{
    public const string ROUTE = 'registration';

    public function __construct(
        private readonly FacultyRepository $facultyRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly UserActions $userActions,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(
        Request $request,
        RequestStack $requestStack,
    ): Response {
        $faculties = $this->facultyRepository->findAllWithTranslations();

        $data = new UserRegistrationDto();

        $form = $this->formFactory->create(RegistrationType::class, $data, [
            'faculties' => $faculties,
        ]);

        $form->handleRequest($requestStack->getMainRequest());

        $responseCode = Response::HTTP_OK;

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $errors = $this->userActions->create($data);

                if ($errors === []) {
                    $this->addFlash('success', 'registration.success');
                    return $this->redirectToRoute(IndexController::ROUTE);
                }

                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $fieldError) {
                        $this->addFlash('error', sprintf(
                            'registration.%s.%s',
                            $field,
                            $fieldError,
                        ));
                    }
                }
            }

            $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return $this->render(
            'registration.html.twig',
            [
                'form' => $form,
            ],
            new Response(status: $responseCode),
        );
    }
}

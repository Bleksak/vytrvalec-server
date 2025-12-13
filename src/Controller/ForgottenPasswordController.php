<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\UserActions;
use App\Dto\User\ForgottenPasswordResetDto;
use App\Form\ForgottenPasswordResetType;
use App\Repository\FacultyRepository;
use App\Repository\UserRepository;
use App\Utils\Toast\ToastContext;
use App\Utils\Toast\ToastManager;
use App\Utils\Toast\ToastType;
use SensitiveParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ForgottenPasswordController extends AbstractController
{
    public function __construct(
        private readonly UserActions $action,
        private readonly ToastManager $toastManager,
        private readonly UserRepository $userRepository,
        private readonly FacultyRepository $facultyRepository,
    ) {}

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    #[Route(
        '/forgotten-password/{passwordResetToken}',
        name: 'forgotten_password',
        methods: [Request::METHOD_GET, Request::METHOD_POST],
    )]
    public function __invoke(
        Request $request,
        #[SensitiveParameter] string $passwordResetToken,
    ): Response {
        $user =
            $this->userRepository->findByPasswordResetToken($passwordResetToken);

        if ($user === null) {
            throw $this->createNotFoundException(
                'user.password_reset.hash_not_found',
            );
        }

        $data = new ForgottenPasswordResetDto($passwordResetToken);
        $form = $this->createForm(ForgottenPasswordResetType::class, $data, []);

        $form->handleRequest($request);

        $responseCode = Response::HTTP_OK;

        if ($form->isSubmitted() && $form->isValid()) {
            $this->action->forgottenPasswordReset($user, $data);

            $this->toastManager->add(
                ToastType::Success,
                ToastContext::PasswordReset,
                message: 'user.password_reset.success',
                addToFlash: true,
            );

            return $this->redirectToRoute(IndexController::ROUTE);
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $responseCode = Response::HTTP_BAD_REQUEST;
        }

        return $this->render(
            'forgotten_password_reset.html.twig',
            [
                'form' => $form,
                'faculties' =>
                    $this->facultyRepository->findAllWithTranslations(),
            ],
            new Response(status: $responseCode),
        );
    }
}

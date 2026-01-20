<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\User\AccountEditDto;
use App\Entity\User;
use App\Form\AccountDeleteFormType;
use App\Form\AccountEditFormType;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/account', name: self::ROUTE, methods: [Request::METHOD_GET])]
final class AccountController extends AbstractController
{
    public const string ROUTE = 'user:account';

    public function __construct(
        private readonly SeasonRepository $seasonRepository,
    ) {}

    public function __invoke(#[CurrentUser] User $user): Response
    {
        $currentSeason = $this->seasonRepository->findCurrentSeason();

        $editForm = $this->createForm(
            AccountEditFormType::class,
            new AccountEditDto($user->mailing, $user->anonymize),
        );

        $deleteForm = $this->createForm(AccountDeleteFormType::class);

        return $this->render('user/account.html.twig', [
            'current_season' => $currentSeason,
            'edit' => $editForm,
            'delete' => $deleteForm,
        ]);
    }
}

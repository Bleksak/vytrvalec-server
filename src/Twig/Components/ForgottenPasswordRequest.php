<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\UserActions;
use App\Controller\IndexController;
use App\Dto\User\ForgottenPasswordRequestDto;
use App\Form\ForgottenPasswordRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ForgottenPasswordRequest extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ForgottenPasswordRequestDto $initialData;

    public function __construct(
        private Security $security,
    ) {
        $this->initialData = new ForgottenPasswordRequestDto();
    }

    #[\Override]
    public function instantiateForm(): FormInterface
    {
        return $this->createForm(
            ForgottenPasswordRequestType::class,
            $this->initialData,
        );
    }

    #[LiveAction]
    public function login(UserActions $action): ?Response
    {
        $this->submitForm();

        /** @var ForgottenPasswordRequestDto */
        $data = $this->getForm()->getData();

        \assert($data->email !== null);

        try {
            $action->forgottenPasswordRequest($data->email);
        } finally {
        }

        $this->addFlash('success', 'user.login.success');
        return $this->redirectToRoute(IndexController::ROUTE);
    }
}

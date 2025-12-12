<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\UserActions;
use App\Controller\IndexController;
use App\Dto\User\UserLoginDto;
use App\Exceptions\User\PasswordInvalidException;
use App\Exceptions\User\UserNotFoundException;
use App\Form\LoginType;
use App\Utils\Toast\ToastContext;
use App\Utils\Toast\ToastManager;
use App\Utils\Toast\ToastType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveResponder;

#[AsLiveComponent]
final class Login extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public UserLoginDto $initialData;

    public function __construct(
        private Security $security,
        private ToastManager $toastManager,
        private LiveResponder $liveResponder,
        private LoggerInterface $logger,
    ) {
        $this->initialData = new UserLoginDto();
    }

    #[\Override]
    public function instantiateForm(): FormInterface
    {
        return $this->createForm(LoginType::class, $this->initialData);
    }

    #[LiveAction]
    public function login(UserActions $action): ?Response
    {
        $this->submitForm();

        /** @var UserLoginDto */
        $data = $this->getForm()->getData();

        try {
            $user = $action->login($data);
        } catch (PasswordInvalidException|UserNotFoundException) {
            $this->toastManager->add(
                ToastType::Error,
                ToastContext::Login,
                message: 'login.user_not_found',
            );

            return null;
        }

        $this->security->login($user);
        $this->toastManager->add(
            ToastType::Success,
            ToastContext::Login,
            message: 'login.success',
            addToFlash: true,
        );

        $this->resetForm();

        return $this->redirectToRoute(IndexController::ROUTE);
    }
}

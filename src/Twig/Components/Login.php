<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\UserActions;
use App\Controller\IndexController;
use App\Dto\User\UserLoginDto;
use App\Exceptions\User\UserNotFoundException;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Login extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public UserLoginDto $initialData;

    public function __construct(
        private Security $security,
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

            $this->security->login($user);
        } catch (InvalidPasswordException|UserNotFoundException) {
            $this->addFlash('error', 'user.not_found');

            return null;
        }

        $this->addFlash('success', 'user.login.success');
        return $this->redirectToRoute(IndexController::ROUTE);
    }
}

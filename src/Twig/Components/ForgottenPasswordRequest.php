<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\UserActions;
use App\Dto\User\ForgottenPasswordRequestDto;
use App\Form\ForgottenPasswordRequestType;
use App\Utils\Toast\ToastContext;
use App\Utils\Toast\ToastManager;
use App\Utils\Toast\ToastType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveResponder;

#[AsLiveComponent]
final class ForgottenPasswordRequest extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ForgottenPasswordRequestDto $initialData;

    public function __construct(
        private LiveResponder $liveResponder,
        private ToastManager $toastManager,
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
    public function submit(UserActions $action): void
    {
        $this->submitForm();

        /** @var ForgottenPasswordRequestDto */
        $data = $this->getForm()->getData();

        \assert($data->email !== null);

        try {
            $action->forgottenPasswordRequest($data->email);
        } finally {
        }

        $this->toastManager->add(
            ToastType::Success,
            ToastContext::ForgottenPasswordRequest,
            message: 'user.forgotten_password_request.success',
        );

        $this->liveResponder->dispatchBrowserEvent('dialog:close');
        $this->resetForm();
    }
}

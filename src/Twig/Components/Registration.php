<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Action\UserActions;
use App\Controller\IndexController;
use App\Dto\UserRegistrationDto;
use App\Entity\Faculty;
use App\Exceptions\User\InvalidFacultySelectedException;
use App\Exceptions\User\TranslatableExceptionInterface;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Registration extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    /** @var array<int, Faculty> */
    #[LiveProp(writable: true)]
    public array $faculties;

    #[LiveProp]
    public ?UserRegistrationDto $initialData = null;

    public function __construct()
    {
        $this->initialData = new UserRegistrationDto();
    }

    #[\Override]
    public function instantiateForm(): FormInterface
    {
        return $this->createForm(RegistrationType::class, $this->initialData, [
            'faculties' => $this->faculties,
        ]);
    }

    /**
     * @param array<int, Faculty> $faculties
     */
    public function mount(array $faculties): void
    {
        $this->faculties = $faculties;
    }

    #[LiveAction]
    public function save(UserActions $action): ?Response
    {
        $this->submitForm();

        /** @var UserRegistrationDto */
        $data = $this->getForm()->getData();

        try {
            $action->create($data);
        } catch (TranslatableExceptionInterface $e) {
            $this->addFlash('error', $e->toTranslatableMessage());

            return null;
        }

        $this->addFlash('success', 'registration.success');
        return $this->redirectToRoute(IndexController::ROUTE);
    }
}

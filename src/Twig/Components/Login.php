<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Form\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Login
{
    public FormView $login;

    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {}

    public function mount(): void
    {
        $form = $this->formFactory->create(LoginType::class);

        $this->login = $form->createView();
    }
}

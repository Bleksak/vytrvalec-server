<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Entity\Faculty;
use App\Form\RegistrationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Registration
{
    public FormView $registration;

    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {}

    /**
     * @param list<Faculty> $faculties
     */
    public function mount(array $faculties): void
    {
        $data = [];
        $form = $this->formFactory->create(RegistrationType::class, $data, [
            'faculties' => $faculties,
        ]);

        $this->registration = $form->createView();
    }
}

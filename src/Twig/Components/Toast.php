<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveResponder;

#[AsLiveComponent]
final class Toast extends AbstractController
{
    use DefaultActionTrait;

    /** @var array<string, array{type: string, message: string, id: string}> */
    #[LiveProp]
    public array $messages = [];

    public function __construct(
        public LiveResponder $responder,
    ) {}

    #[LiveListener('toast-add')]
    public function toastAddedHandler(
        #[LiveArg] string $type,
        #[LiveArg] string $message,
        #[LiveArg] string $id,
    ): void {
        if (
            isset($this->messages[$id])
            && $this->messages[$id]['type'] === $type
        ) {
            return;
        }

        $this->messages[$id] = [
            'type' => $type,
            'id' => $id,
            'message' => $message,
        ];
    }

    #[LiveListener('toast-remove')]
    public function toastRemoveHandler(#[LiveArg] string $id): void
    {
        unset($this->messages[$id]);
    }
}

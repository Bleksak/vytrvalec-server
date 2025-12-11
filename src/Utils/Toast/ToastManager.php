<?php

declare(strict_types=1);

namespace App\Utils\Toast;

use Symfony\UX\LiveComponent\LiveResponder;

final readonly class ToastManager
{
    public function __construct(
        private LiveResponder $liveResponder,
    ) {}

    public function add(
        ToastType $type,
        ToastContext $toastContext,
        string $message,
    ): void {
        $this->liveResponder->emit('toast-add', [
            'type' => $type->value,
            'id' => $toastContext->value,
            'message' => $message,
        ]);
    }
}

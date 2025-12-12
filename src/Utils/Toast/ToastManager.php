<?php

declare(strict_types=1);

namespace App\Utils\Toast;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\UX\LiveComponent\LiveResponder;

final readonly class ToastManager
{
    public function __construct(
        private LiveResponder $liveResponder,
        private RequestStack $requestStack,
    ) {
    }

    public function add(
        ToastType $type,
        ToastContext $toastContext,
        string $message,
        bool $addToFlash = false,
    ): void {
        if ($addToFlash === true) {
            $key = \sprintf('toast.%s.%s', $toastContext->value, $type->value);

            $session = $this->requestStack->getSession();

            if ($session instanceof FlashBagAwareSessionInterface) {
                $flashBag = $session->getFlashBag();
                $flashBag->add($key, $message);
            }
        } else {
            $this->liveResponder->emit('toast-add', [
                'type' => $type->value,
                'id' => $toastContext->value,
                'message' => $message,
            ]);
        }
    }
}

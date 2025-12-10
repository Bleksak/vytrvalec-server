<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Toast
{
    use DefaultActionTrait;
    private const array TOAST_TYPES = ['success', 'error', 'alert', 'info', 'warning'];

    #[LiveProp(writable: true)]
    public array $messages = [];

    public function __construct(private RequestStack $requestStack) {}

    public function mount(): void
    {
        $this->loadFlashMessages();
    }

    #[LiveAction]
    public function refresh(): void
    {
        $this->loadFlashMessages();
    }

    public function getVisibleMessages(): array
    {
        return $this->messages;
    }

    private function loadFlashMessages(): void
    {
        $session = $this->requestStack->getSession();
        $flashBag = $session->getFlashBag();
        foreach (self::TOAST_TYPES as $type) {
            foreach ($flashBag->get($type) as $message) {
                $this->messages[] = [
                    'type' => $type,
                    'message' => $message,
                ];
            }
        }
    }
}

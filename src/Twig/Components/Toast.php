<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Utils\Toast\ToastManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
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
        private LiveResponder $responder,
        private ToastManager $_toastManager,
        private RequestStack $requestStack,
    ) {
        $this->fillFromSession();
    }

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

    #[PreReRender]
    private function fillFromSession()
    {
        $session = $this->requestStack->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            $flashBag = $session->getFlashBag();

            /** @var array<string, list<string>> */
            $flashBagMessages = $flashBag->peekAll();

            foreach ($flashBagMessages as $type => $messages) {
                $matches = [];

                $matched = \preg_match(
                    '/(?P<toast>toast)\.(?P<toast_context>[A-Za-z0-9\-]+)\.(?P<toast_type>[A-Za-z0-9\-]+)/',
                    $type,
                    $matches,
                );

                if ($matched === false) {
                    continue;
                }

                $flashBag->get($type);

                \assert(
                    isset(
                        $matches['toast'],
                        $matches['toast_context'],
                        $matches['toast_type'],
                    ),
                );

                $toastContext = $matches['toast_context'];
                $toastType = $matches['toast_type'];

                if ($toastContext === null || $toastType === null) {
                    continue;
                }

                foreach ($messages as $message) {
                    $this->toastAddedHandler(
                        $toastType,
                        $message,
                        $toastContext,
                    );
                }
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Translation\LocaleSwitcher;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onSuccessLogin')]
#[AsEventListener(event: RequestEvent::class, method: 'onRequest')]
final class LocaleSubscriber
{
    private bool $switchedFromRequest = false;

    private const ALLOWED_LOCALES = ['cs_CZ', 'en_US'];

    public function __construct(
        private LocaleSwitcher $localeSwitcher,
    ) {
    }

    public function onSuccessLogin(LoginSuccessEvent $event): void
    {
        if ($this->switchedFromRequest) {
            return;
        }

        $user = $event->getUser();
        assert($user instanceof User);

        $this->localeSwitcher->setLocale($user->getLocale());
    }

    public function onRequest(RequestEvent $event): void
    {
        $requestedLocale = $event->getRequest()->get('locale');

        if (!is_string($requestedLocale)) {
            return;
        }

        if (!in_array($requestedLocale, self::ALLOWED_LOCALES)) {
            return;
        }

        $this->localeSwitcher->setLocale($requestedLocale);
        $this->switchedFromRequest = true;
    }
}

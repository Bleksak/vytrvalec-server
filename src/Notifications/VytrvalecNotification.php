<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Entity\User;
use App\Notifications\Firebase\AbstractFirebaseNotification;
use Kreait\Firebase\Messaging\Message;

final readonly class VytrvalecNotification extends AbstractFirebaseNotification implements Message
{
    public function __construct(string|User $recipient, string $message, ?string $action = null)
    {
        if ($recipient instanceof User) {
            $recipient = $recipient->getToken();
        }

        if ($recipient === null || $recipient === '') {
            return;
        }

        parent::__construct($recipient, 'Měsíční vytrvalec', $message, $action);
    }

    #[\Override]
    public function jsonSerialize(): mixed
    {
        return [
            'to' => $this->to,
            'message' => $this->message,
            'action' => $this->action,
        ];
    }
}

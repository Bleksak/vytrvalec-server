<?php

namespace App\Notifications;

use App\Entity\User;
use App\Notifications\Firebase\FirebaseNotification;
use Kreait\Firebase\Messaging\Message;

final class VytrvalecNotification extends FirebaseNotification implements Message
{
    public function __construct(
        string|User $recipient,
        string $message,
        ?string $action = null,
    ) {
        if ($recipient instanceof User) {
            $recipient = $recipient->getToken();
        }

        parent::__construct($recipient, 'Měsíční vytrvalec', $message, $action);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'to' => $this->to(),
            'message' => $this->message(),
            'action' => $this->action(),
        ];
    }
}

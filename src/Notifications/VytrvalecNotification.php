<?php

namespace App\Notifications;

use App\Entity\User;
use App\Notifications\Firebase\FirebaseNotification;

final class VytrvalecNotification extends FirebaseNotification
{
    public function __construct(
        User $recipient,
        string $message,
        ?string $action = null,
    ) {
        parent::__construct($recipient->getToken(), 'Měsíční vytrvalec', $message, $action);
    }
}

<?php

namespace App\Notifications;

use App\Entity\User;
use App\Notifications\Firebase\FirebaseNotification;

class VytrvalecNotification extends FirebaseNotification
{
    // TODO: Translations
    public function __construct(User $recipient, private readonly string $message, private readonly ?string $action = null)
    {
        parent::__construct($recipient->getToken(), 'Měsíční vytrvalec', $message, $action);
    }
}

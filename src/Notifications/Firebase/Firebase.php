<?php

declare(strict_types=1);

namespace App\Notifications\Firebase;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MessageData;
use Kreait\Firebase\Messaging\Notification;

final readonly class Firebase
{
    public function __construct(
        private Messaging $messaging,
    ) {
    }

    public function send(AbstractFirebaseNotification $notification): bool
    {
        try {
            $this->messaging->send(
                CloudMessage::new()
                    ->toToken($notification->to)
                    ->withData(MessageData::fromArray(
                        [
                            'notification_type' => '',
                        ]
                    ))
                    ->withNotification(
                        Notification::create(
                            $notification->title,
                            $notification->message,
                            null,
                        )
                    )
            );
        } catch (\Throwable) {
            return false;
        }

        return true;
    }
}

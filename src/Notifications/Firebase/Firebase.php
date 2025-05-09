<?php

declare(strict_types=1);

namespace App\Notifications\Firebase;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MessageData;
use Kreait\Firebase\Messaging\Notification;

final readonly class Firebase
{
    public function __construct(
        private Messaging $messaging,
    ) {
    }

    public function send(
        AbstractFirebaseNotification $notification,
    ): bool {
        try {
            // @phpstan-ignore-next-line
            $this->messaging->send([
                'token' => $notification->to(),
                // 'topic' => null,
                // 'condition' => null,
                'data' => MessageData::fromArray([
                    'notification_type' => '',
                ]),
                'notification' => Notification::create($notification->title(), $notification->message(), null), // ?: Notification|NotificationShape,
                // 'android' => null, // ?: AndroidConfigShape,
                // 'apns' => null, // ?: ApnsConfig|ApnsConfigShape,
                // 'webpush' => null, // ?: WebPushConfig|WebPushConfigShape,
                // 'fcm_options' => null, // ?: FcmOptions|FcmOptionsShape
            ], false);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}

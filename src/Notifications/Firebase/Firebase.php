<?php

namespace App\Notifications\Firebase;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Firebase\FirebaseTransport;
use Symfony\Component\Notifier\Bridge\Firebase\FirebaseTransportFactory;
use Symfony\Component\Notifier\Bridge\Firebase\Notification\WebNotification;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Transport\Dsn;

class Firebase
{
    private FirebaseTransport $firebase;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->firebase = (new FirebaseTransportFactory())
            ->create(new Dsn($parameterBag->get('firebase_dsn')));
    }

    public function send(FirebaseNotification $notification): ?SentMessage
    {
        $webNotification = (new WebNotification($notification->to(), []))
            ->title($notification->title())
        ;

        if($notification->action() !== null) {
            $webNotification->clickAction($notification->action());
        }

        $message = (new ChatMessage($notification->message()))
            ->options($webNotification)
        ;

        try {
            return $this->firebase->send($message);
        } catch(TransportException) {
            return null;
        }
    }
}

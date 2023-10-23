<?php

namespace App\Notifications\Firebase;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\Bridge\Firebase\FirebaseTransport;
use Symfony\Component\Notifier\Bridge\Firebase\FirebaseTransportFactory;
use Symfony\Component\Notifier\Bridge\Firebase\Notification\AndroidNotification;
use Symfony\Component\Notifier\Bridge\Firebase\Notification\WebNotification;
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

    public function send(FirebaseNotification $notification): SentMessage
    {
        $webNotification = (new AndroidNotification($notification->to(), []))
            ->title($notification->title())
        ;

        if($notification->action() !== null) {
            $webNotification->clickAction($notification->action());
        }

        $message = (new ChatMessage($notification->message()))
            ->options($webNotification)
        ;

        return $this->firebase->send($message);
    }
}
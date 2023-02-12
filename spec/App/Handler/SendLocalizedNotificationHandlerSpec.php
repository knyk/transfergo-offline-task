<?php

namespace spec\App\Handler;

use App\Command\SendLocalizedNotification;
use App\Handler\SendLocalizedNotificationHandler;
use App\Service\NotificationSender;
use App\Service\Translation\NotificationTranslator;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use PhpSpec\ObjectBehavior;

class SendLocalizedNotificationHandlerSpec extends ObjectBehavior
{
    public function it_translates_notification_and_sends_it(
        NotificationTranslator $notificationTranslator,
        NotificationSender $notificationSender
    ): void {
        $this->beConstructedWith($notificationTranslator, $notificationSender);

        $command = new SendLocalizedNotification(
            'receiver',
            'contentTranslationKey',
            Channel::Email,
            'pl_PL',
            'subjectTranslationKey',
        );

        $translatedNotification = new Notification(
            $command->channel,
            $command->receiver,
            'translatedContent',
            'translatedSubject'
        );

        $notificationTranslator->translate(
            new Notification(
                $command->channel,
                $command->receiver,
                $command->contentTranslationKey,
                $command->subjectTranslationKey
            ),
            $command->locale
        )->shouldBeCalledOnce()->willReturn($translatedNotification);

        $notificationSender->send($translatedNotification)->shouldBeCalledOnce();

        $this->__invoke($command);
    }
}

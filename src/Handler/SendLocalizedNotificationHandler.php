<?php

declare(strict_types=1);

namespace App\Handler;

use App\Command\SendLocalizedNotification;
use App\Service\NotificationSender;
use App\Service\Translation\NotificationTranslator;
use App\ValueObject\Notification;

final readonly class SendLocalizedNotificationHandler
{
    public function __construct(
        private NotificationTranslator $notificationTranslator,
        private NotificationSender $notificationSender
    ) {
    }

    public function __invoke(SendLocalizedNotification $command): void
    {
        $notification = new Notification(
            $command->channel,
            $command->receiver,
            $command->contentTranslationKey,
            $command->subjectTranslationKey
        );
        $translatedNotification = $this->notificationTranslator->translate($notification, $command->locale);

        $this->notificationSender->send($translatedNotification);
    }
}

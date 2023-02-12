<?php

declare(strict_types=1);

namespace App\Service\Translation;

use App\ValueObject\Notification;

class NotificationTranslator
{
    private const FALLBACK_LOCALE = 'en_US';

    public function __construct(private readonly Translator $translator)
    {
    }

    public function translate(Notification $notification, string $locale): Notification
    {
        $content = $this->getTranslation($notification->content, $locale);
        $subject = $notification->subject ? $this->getTranslation($notification->subject, $locale) : null;

        return new Notification($notification->channel, $notification->receiver, $content, $subject);
    }

    private function getTranslation(string $key, $locale): string
    {
        return $this->translator->translate($key, $locale, self::FALLBACK_LOCALE);
    }
}

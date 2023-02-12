<?php

declare(strict_types=1);

namespace App\Service\Translation;

final class NotFoundTranslationKey extends \RuntimeException
{
    public static function withKeyAndLocale(string $key, string $locale): self
    {
        return new self(sprintf('Translation key "%s" not found for locale "%s"', $key, $locale));
    }
}

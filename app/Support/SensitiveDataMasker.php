<?php

namespace App\Support;

class SensitiveDataMasker
{
    public static function mask(?string $value, int $visibleChars = 3): string
    {
        if (empty($value)) {
            return '';
        }

        $length = strlen($value);

        if ($length <= $visibleChars) {
            return $value;
        }

        return str_repeat('*', $length - $visibleChars).substr($value, -$visibleChars);
    }
}

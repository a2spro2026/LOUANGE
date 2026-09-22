<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class DateFormat
{
    public const DISPLAY = 'd/m/Y';

    public const PLACEHOLDER = 'jj/mm/aaaa';

    public static function format(mixed $date): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        return Carbon::parse($date)->format(self::DISPLAY);
    }

    public static function maskInput(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
        $digits = substr($digits, 0, 8);

        if (strlen($digits) <= 2) {
            return $digits;
        }

        if (strlen($digits) <= 4) {
            return substr($digits, 0, 2).'/'.substr($digits, 2);
        }

        return substr($digits, 0, 2).'/'.substr($digits, 2, 2).'/'.substr($digits, 4);
    }

    public static function toDatabase(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = self::maskInput($value);

        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return Carbon::createFromFormat(self::DISPLAY, $value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (InvalidFormatException) {
            return null;
        }
    }

    public static function isValid(?string $value): bool
    {
        return self::toDatabase($value) !== null;
    }
}

<?php

namespace App\Support;

class MoneyFormat
{
    public static function format(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    public static function parse(mixed $value): float
    {
        if (is_numeric($value)) {
            return round((float) $value, 2);
        }

        $normalized = str_replace([' ', ','], ['', '.'], (string) $value);

        return round((float) $normalized, 2);
    }
}

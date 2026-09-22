<?php

use App\Support\DateFormat;
use App\Support\MoneyFormat;

if (! function_exists('date_fr')) {
    function date_fr(mixed $date): string
    {
        return DateFormat::format($date);
    }
}

if (! function_exists('montant_fr')) {
    function montant_fr(mixed $amount): string
    {
        return MoneyFormat::format($amount);
    }
}

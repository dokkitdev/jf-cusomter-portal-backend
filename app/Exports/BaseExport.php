<?php

namespace App\Exports;

class BaseExport
{
    protected function getDescription(?string $string): ?string
    {
        $strippedString = str_replace("&nbsp;", ' ', strip_tags($string, 'null'));

        if ($strippedString) {
            $exploded = explode('.', $strippedString);
            $trimmed = array_map('trim', $exploded);
            $strippedString = implode('. ', $trimmed);
        }

        return $strippedString;
    }
}
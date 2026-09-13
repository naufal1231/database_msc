<?php

namespace App\Helpers;

class TimeHelper
{
    /**
     * Convert MM.SS.CC string to total centiseconds (1/100 second)
     */
    public static function toCentiseconds(string $timeString): int
    {
        if (!preg_match('/^(\d{2})\.(\d{2})\.(\d{2})$/', $timeString, $matches)) {
            return 0;
        }

        $minutes = (int)$matches[1];
        $seconds = (int)$matches[2];
        $centiseconds = (int)$matches[3];

        return ($minutes * 6000) + ($seconds * 100) + $centiseconds;
    }

    /**
     * Convert total centiseconds back to MM.SS.CC string format
     */
    public static function fromCentiseconds(int $totalCentiseconds): string
    {
        $minutes = floor($totalCentiseconds / 6000);
        $rem = $totalCentiseconds % 6000;
        
        $seconds = floor($rem / 100);
        $centiseconds = $rem % 100;

        return sprintf('%02d.%02d.%02d', $minutes, $seconds, $centiseconds);
    }
}

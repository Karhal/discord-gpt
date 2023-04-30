<?php

namespace Bot\Utils;

abstract class DateUtils
{
    public static function isTimestampExpired(int $current, int $timestamp, int $ttl): bool
    {
        $difference = $current - $timestamp;

        return ($difference > $ttl);
    }
}

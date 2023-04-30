<?php

namespace Tests\Utils;

use PHPUnit\Framework\TestCase;
use Bot\Utils\DateUtils;

class DateUtilsTest extends TestCase
{
    public function testGeneratePromptFromHistory() 
    {
        $ttl = 600;

        $this->assertTrue(DateUtils::isTimestampExpired(time()+800, time(), $ttl));
    }
}
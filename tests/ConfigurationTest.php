<?php

namespace Tests;

use Bot\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testGetconfig()
    {
        $configuration = new Configuration();

        $this->assertIsArray($configuration->getConfig());
    }
}
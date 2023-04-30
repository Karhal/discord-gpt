<?php

namespace Bot;

use Symfony\Component\Yaml\Yaml;

class Configuration
{
    private $config;

    public function __construct()
    {
        $this->config = Yaml::parseFile(__DIR__.'/../config.yaml');

        if (file_exists(__DIR__.'/../config.override.yaml')) {
            $this->config = array_replace_recursive($this->config, Yaml::parseFile(__DIR__.'/../config.override.yaml'));
        }
    }

    public function getConfig()
    {
        return $this->config;
    }
}

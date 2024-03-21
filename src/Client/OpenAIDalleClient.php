<?php

namespace Bot\Client;

use Bot\Configuration;
use Psr\Log\LoggerInterface;

class OpenAIDalleClient
{
    public const BASE_URL = 'https://api.openai.com/v1/images/generations';

    private $config;
    private LoggerInterface $logger;
    
    public function __construct(Configuration $configuration, LoggerInterface $logger)
    {
        $this->config = $configuration->getConfig()['openai-dalle'];
        $this->logger = $logger;
    }

    public function generateImage(string $prompt): string
    {
        $url = self::BASE_URL;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->config['key'],
        ];
        $data = $this->config['config'];
        $data['prompt'] = $prompt;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (false === $response) {
            exit('cURL error: '.curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }
}

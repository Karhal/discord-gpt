<?php

namespace Bot\Client;

use Bot\Configuration;
use Psr\Log\LoggerInterface;

class OpenAIVisionClient
{
    public const BASE_URL = 'https://api.openai.com/v1/chat/completions';

    private $config;
    private LoggerInterface $logger;
    private int $tries = 0;

    public function __construct(Configuration $configuration, LoggerInterface $logger)
    {
        $this->config = $configuration->getConfig()['openai-vision'];
        $this->logger = $logger;
    }

    public function getDescription(array $prompt): string
    {
        $response = $this->get($prompt);
        $content = json_decode($response, true);

        if (!array_key_exists('choices', $content)) {
            ++$this->tries;
            if ($this->tries >= 5) {
                return 'I\'m lagging';
            }
            sleep(1);
            echo 'RETRY'.PHP_EOL;

            return $this->getDescription($prompt);
        }

        return $content['choices'][0]['message']['content'];
    }

    private function get(array $prompt): string
    {
        $ch = curl_init(self::BASE_URL);
        $conf = $this->getConf($prompt);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->config['key'],
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $conf);

        $this->logger->info(json_encode($prompt), ['conf' => $this->config['config']]);
        $response = curl_exec($ch);
        $this->logger->info($response);

        if (false === $response) {
            exit('cURL error: '.curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    private function getConf(array $prompt): string
    {
        $data = $this->config['config'];
        $data['messages'] = array_merge($data['messages'], $prompt);

        return json_encode($data);
    }

    public function hasImage($message) : bool
    {
        return preg_match('/(http(s?):)([/|.|\w|\s|-])*\.(?:jpg|gif|png)/', $message);
    }

    public function extractImage($message) : string
    {
        preg_match('/(http(s?):)([/|.|\w|\s|-])*\.(?:jpg|gif|png)/', $message, $matches);
        return $matches[0];
    }
}

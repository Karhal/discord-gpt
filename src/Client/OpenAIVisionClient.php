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

    public function getDescription(string $imageUrl): string
    {

        $response = $this->get($imageUrl);
        $content = json_decode($response, true);

        if (!array_key_exists('choices', $content)) {
            ++$this->tries;
            if ($this->tries >= 5) {
                return 'I\'m lagging';
            }
            sleep(1);
            echo 'RETRY'.PHP_EOL;

            return $this->getDescription($imageUrl);
        }

        return $content['choices'][0]['message']['content'];
    }

    private function get(string $imageUrl): string
    {
        $ch = curl_init(self::BASE_URL);
        $body = $this->completePromptWithImageData($imageUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->config['key'],
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $this->logger->info(json_encode($body), ['conf' => $this->config['config']]);
        $response = curl_exec($ch);
        $this->logger->info($response);

        if (false === $response) {
            exit('cURL error: '.curl_error($ch));
        }
        curl_close($ch);

        return $response;
    }

    private function completePromptWithImageData(string $imageUrl): string
    {
        $data = $this->config['config'];
        $data['messages'] = [
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "text",
                        "text" => "What’s in this image?"
                    ],
                    [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => "https://upload.wikimedia.org/wikipedia/commons/thumb/d/dd/Gfp-wisconsin-madison-the-nature-boardwalk.jpg/2560px-Gfp-wisconsin-madison-the-nature-boardwalk.jpg"
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($data);
    }
}

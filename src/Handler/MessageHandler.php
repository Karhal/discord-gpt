<?php

namespace Bot\Handler;

use Bot\Configuration;
use Bot\Model\Message;

class MessageHandler
{
    private array $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config->getConfig();
    }

    public function createMessage(string $author, string $authorId, string $content, string $id, string $dateTime, int $channelId, bool $isAssistant = false): Message
    {
        $content = str_replace($this->config['discord']['botUserId'], $this->config['openai']['botExternalName'], $content);
        $message = new Message();
        $message->id = $id;
        $message->author = $author;
        $message->authorId = $authorId;
        $message->message = $content;
        $message->channelId = $channelId;
        $message->timestamp = (new \DateTime($dateTime))->getTimestamp();
        $message->isAssistant = $isAssistant;
        ;

        return $message;
    }

    public function hasImage(Message $message) : bool
    {
        return preg_match('/(http(s?):)([\/.|\\w|\\s|-])*\.(?:jpg|gif|png)/', $message->message);
        
    }

    public function extractImage(Message $message) : string
    {
        preg_match('/(http(s?):)([\/.|\\w|\\s|-])*\.(?:jpg|gif|png)/', $message->message, $matches);
        return $matches[0];
    }

    public function generateAndSendImage($completion, $application, $channel) {
        var_dump($completion->image_prompt);
        $image = json_decode($application->openAIDalleClient->generateImage($completion->image_prompt));
        if(property_exists($image, 'data') === false) {
            echo "data not found, trying again\n";
            $image = json_decode($application->openAIDalleClient->generateImage($completion->image_prompt));
        }
        $application->logger->info("Image generated: ".$image->data[0]->url);
        $file = file_get_contents($image->data[0]->url);
        $filename = base64_encode(time());
        $filePath = "./tmp/".$filename.".png";
        file_put_contents($filePath, $file);
        $channel->sendFile($filePath);
        unlink($filePath);
    }
}

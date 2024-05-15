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
<<<<<<< Updated upstream
=======

    public function hasImage(BotMessage $message) : bool
    {
        return preg_match('/(http(s?):)([\/.|\\w|\\s|-])*\.(?:jpg|gif|png)/', $message->message);
        
    }

    public function extractImage(BotMessage $message) : string
    {
        preg_match('/(http(s?):)([\/.|\\w|\\s|-])*\.(?:jpg|gif|png)/', $message->message, $matches);
        return $matches[0];
    }

    public function generateAndSendImage($completion, $application, $channel) {
        echo "Image Prompt:";
        var_dump($completion->image_prompt);

        if($completion->image_prompt === "") {
            return;
        }
        $image = json_decode($application->openAIDalleClient->generateImage($completion->image_prompt));
        if(property_exists($image, 'data') === false) {
            echo "data not found skipping";
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

    public function sendMessage(Application $application, Channel $channel, BotMessage $appMessage) {
        $channelList = $application->historyList->getChannelList($channel->id);
    
        if ($application->messageHandler->hasImage($appMessage) && $imageUrl = $application->messageHandler->extractImage($appMessage)) {
            $this->handleImageDescription($imageUrl, $application, $appMessage, $channel);
        }
    
        $conversationHistory = $application->promptHandler->generatePromptFromHistory($channelList);
        $completion = json_decode($application->openAIClient->getCompletion($conversationHistory));
    
        if (empty($completion)) {
            return;
        }
        echo "Image:   ";
        var_dump($completion->image);
        if ((bool) $completion->image === true) {
            $application->messageHandler->generateAndSendImage($completion, $application, $channel);
        }
    
        if ($completion !== end($channelList)->message) {
            $channel->sendMessage($completion->response);
        }
    }

    public function handleImageDescription(string $imageUrl, Application $application, BotMessage $appMessage, Channel $channel)
    {
        $imageDescription = $application->openAIVisionClient->getDescription($imageUrl);
        if (empty($imageDescription)) {
            return;
        }
        var_dump($imageDescription);
        $imageDescriptionMessage = $this->createMessage($appMessage->author, $appMessage->id . rand(), "sent a picture described as: " . $imageDescription, uniqid(), (new \DateTime())->format('Y-m-d H:i:s'), $channel->id, true);
        $application->historyListHandler->addMessageToHistory($application->historyList, $imageDescriptionMessage);
    }

    public function handleNewMessage(Message $message, Discord $discord, Application $application)
    {
        $channel = $message->channel;
        $appMessage = $this->createMessage($message->author->username, $message->author->id, $message->content, $message->id, $message->timestamp, $channel->id, ($discord->user->id === $message->author->id));
        
        if (!$application->botHandler->shouldIAnswer($discord->user->id, $appMessage)) {
            return;
        }

        $application->historyListHandler->addMessageToHistory($application->historyList, $appMessage);

        sleep(rand(0, 2));

        $channel->broadcastTyping()->then(function () use ($application, $channel, $appMessage) {
            $this->sendMessage($application, $channel, $appMessage);
        });
    }
>>>>>>> Stashed changes
}

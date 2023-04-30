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

    public function createMessage(string $author, string $authorId, string $content, string $id, string $dateTime, int $channelId): Message
    {
        $content = str_replace($this->config['discord']['botUserId'], $this->config['openai']['botExternalName'], $content);
        $message = new Message();
        $message->id = $id;
        $message->author = $author;
        $message->authorId = $authorId;
        $message->message = $content;
        $message->channelId = $channelId;
        $message->timestamp = (new \DateTime($dateTime))->getTimestamp();
        ;

        return $message;
    }
}

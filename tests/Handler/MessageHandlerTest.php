<?php

namespace Tests\Handler;

use Bot\Configuration;
use Bot\Handler\MessageHandler;
use PHPUnit\Framework\TestCase;

class MessageHandlerTest extends TestCase
{
    public function testCreateMessage(int $count = 1)
    {
        $conf = new Configuration();
        $messageHandler = new MessageHandler($conf);
        $author = 'Bob';
        $authorId = '1';
        $content = 'Lorem Ipsum';
        $id = '1';
        $dateTime = (new \DateTime());
        $channelId = 1;
        $message = $messageHandler->createMessage($author, $authorId, $content, $id , $dateTime->format(DATE_RSS), $channelId);

        $this->assertEquals($author, $message->author);
        $this->assertEquals($authorId, $message->authorId);
        $this->assertEquals($content, $message->message);
        $this->assertEquals($id, $message->id);
        $this->assertEquals($dateTime->getTimestamp(), $message->timestamp);
        $this->assertEquals($channelId, $message->channelId);
    }
}
<?php

namespace Tests\Handler;

use PHPUnit\Framework\TestCase;
use Bot\Configuration;
use Bot\Handler\BotHandler;
use Bot\Model\Message;

class BotHandlerTest extends TestCase
{
    public function testShouldIAnswer()
    {
        $conf = new Configuration();
        $botHandler = new BotHandler($conf);

        $message = new Message();
        $message->author = 'Bob';
        $message->authorId = '1';
        $message->message = sprintf('Bonjour comment ça va %s ?', $conf->getConfig()['openai']['botExternalName']);
        $message->timestamp = (new \DateTime())->getTimestamp();

        $this->assertTrue($botHandler->shouldIAnswer('2', $message));

        $message = new Message();
        $message->author = 'Bob';
        $message->authorId = '1';
        $message->message = sprintf('Bonjour comment ça va %s ?', $conf->getConfig()['openai']['botExternalName']);
        $message->timestamp = (new \DateTime())->getTimestamp();

        $this->assertFalse($botHandler->shouldIAnswer('2', $message));
    }
  
}